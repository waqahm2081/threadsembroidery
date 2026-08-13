<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\CartItemFile;
use App\Models\PlacementOption;
use App\Models\UserArtwork;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CartService
{
    public const VAT_RATE = 0.20;

    public const FREE_DELIVERY_OVER = 150.0;

    public const DELIVERY_FEE = 6.95;

    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()])->load(['items.files']);
        }

        return Cart::firstOrCreate(
            ['session_id' => session()->getId(), 'user_id' => null]
        )->load(['items.files']);
    }

    public function mergeGuestCartForUser(int $userId, string $guestSessionId): void
    {
        if ($guestSessionId === '') {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);
        $guestCart = Cart::query()
            ->where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->first();

        if (! $guestCart || $guestCart->id === $userCart->id) {
            return;
        }

        $guestCart->items()->update(['cart_id' => $userCart->id]);
        $guestCart->delete();
    }

    public function cloneOrderItemsToCart(\App\Models\Order $order): int
    {
        $cart = $this->getCart();
        $count = 0;

        foreach ($order->items as $item) {
            $cartItem = $cart->items()->create([
                'item_type' => $item->item_type,
                'bundle_id' => $item->bundle_id,
                'title' => $item->title,
                'image_path' => null,
                'colour_name' => $item->colour_name,
                'colour_hex' => $item->colour_hex,
                'method' => $item->method,
                'size_breakdown' => $item->size_breakdown,
                'garments_snapshot' => $item->garments_snapshot,
                'addons' => $item->addons,
                'quantity' => $item->quantity,
                'base_price' => $item->base_price,
                'addon_total' => $item->addon_total,
                'line_total' => $item->line_total,
                'summary' => 'Reorder · '.($item->summary ?? $item->title),
            ]);

            foreach ($item->files as $file) {
                $path = $this->copyArtworkToCartItem($cartItem->id, $file->path);

                CartItemFile::create([
                    'cart_item_id' => $cartItem->id,
                    'placement_slug' => $file->placement_slug,
                    'placement_label' => $file->placement_label,
                    'path' => $path,
                    'original_name' => $file->original_name,
                ]);
            }

            $count++;
        }

        return $count;
    }

    private function copyArtworkToCartItem(int $cartItemId, string $sourcePath): string
    {
        $disk = Storage::disk('public');
        $filename = basename($sourcePath) ?: ('artwork-'.uniqid('', true).'.bin');
        $destination = 'cart-artwork/'.$cartItemId.'/'.$filename;

        if ($sourcePath !== '' && $disk->exists($sourcePath)) {
            // Avoid clobbering if basename already exists in the destination folder
            if ($disk->exists($destination)) {
                $destination = 'cart-artwork/'.$cartItemId.'/'.uniqid('', true).'_'.$filename;
            }
            $disk->copy($sourcePath, $destination);

            return $destination;
        }

        return $sourcePath;
    }

    public function itemCount(): int
    {
        return (int) $this->getCart()->items()->sum('quantity');
    }

    public function totals(?Cart $cart = null): array
    {
        $cart ??= $this->getCart();
        $subtotal = round((float) $cart->items->sum('line_total'), 2);
        $vat = round($subtotal * self::VAT_RATE, 2);
        $delivery = $subtotal >= self::FREE_DELIVERY_OVER ? 0.0 : self::DELIVERY_FEE;
        $total = round($subtotal + $vat + $delivery, 2);

        return [
            'subtotal' => $subtotal,
            'vat' => $vat,
            'delivery' => $delivery,
            'total' => $total,
            'item_count' => $cart->items->count(),
            'garment_count' => (int) $cart->items->sum('quantity'),
            'line_count' => $cart->items->count(),
        ];
    }

    public function toFrontend(): array
    {
        $cart = $this->getCart();

        return [
            'items' => $cart->items->map->toFrontend()->values()->all(),
            'totals' => $this->totals($cart),
        ];
    }

    /**
     * @param  array<string, UploadedFile|null>  $files
     * @param  array<string, int>  $libraryIds  placement slug => user_artwork id
     */
    public function addBundle(array $data, array $files = [], array $libraryIds = []): CartItem
    {
        $bundle = Bundle::query()
            ->where('slug', $data['bundle_slug'])
            ->where('is_active', true)
            ->with('garments')
            ->firstOrFail();

        $method = $data['method'];
        if (! in_array($method, $bundle->methods ?? [], true)) {
            throw ValidationException::withMessages(['method' => 'Invalid branding method for this bundle.']);
        }

        $sizeBreakdown = $data['sizes'] ?? [];
        foreach ($bundle->garments as $index => $garment) {
            $row = $sizeBreakdown[(string) $index] ?? $sizeBreakdown[$index] ?? [];
            $sum = collect($row)->sum(fn ($v) => (int) $v);
            if ($sum !== (int) $garment->qty) {
                throw ValidationException::withMessages([
                    'sizes' => "Sizes for {$garment->label} must total {$garment->qty}.",
                ]);
            }
        }

        $totalGarments = (int) $bundle->garments->sum('qty');
        $addonSlugs = array_values(array_filter($data['addons'] ?? []));
        $addonModels = PlacementOption::query()
            ->whereIn('slug', $addonSlugs)
            ->where('is_active', true)
            ->where('included_in_bundle', false)
            ->get();

        $addons = [];
        $addonTotal = 0.0;
        foreach ($addonModels as $placement) {
            if ($placement->method !== 'both' && $placement->method !== $method) {
                continue;
            }
            $line = round((float) $placement->price * $totalGarments, 2);
            $addonTotal += $line;
            $addons[] = [
                'id' => $placement->slug,
                'label' => $placement->label,
                'price' => (float) $placement->price,
                'line' => $line,
            ];
        }

        $basePrice = (float) $bundle->price;
        $lineTotal = round($basePrice + $addonTotal, 2);
        $addonLabels = collect($addons)->pluck('label')->all();
        $summary = trim(sprintf(
            '%s · %d garments · Left chest%s',
            $data['colour_name'],
            $totalGarments,
            $addonLabels ? ' + '.implode(', ', $addonLabels) : ''
        ));

        $cart = $this->getCart();
        $item = $cart->items()->create([
            'item_type' => 'bundle',
            'bundle_id' => $bundle->id,
            'title' => $bundle->name.' (Bundle)',
            'image_path' => null,
            'colour_name' => $data['colour_name'],
            'colour_hex' => $data['colour_hex'] ?? null,
            'method' => $method === 'embroidery' ? 'Embroidery' : 'DTF Print',
            'size_breakdown' => $sizeBreakdown,
            'garments_snapshot' => $bundle->garments->map(fn ($g) => [
                'label' => $g->label,
                'qty' => $g->qty,
                'cat' => $g->category_slug,
            ])->values()->all(),
            'addons' => $addons,
            'quantity' => $totalGarments,
            'base_price' => $basePrice,
            'addon_total' => $addonTotal,
            'line_total' => $lineTotal,
            'summary' => $summary,
        ]);

        $labels = $data['artwork_labels'] ?? [];
        $this->storeArtworkFiles($item, $files, $labels);
        $this->attachLibraryArtwork($item, $libraryIds, $labels, array_keys($files));

        return $item->load('files');
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        foreach ($item->files as $file) {
            Storage::disk('public')->delete($file->path);
        }

        $item->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        foreach ($cart->items as $item) {
            foreach ($item->files as $file) {
                Storage::disk('public')->delete($file->path);
            }
        }
        $cart->items()->delete();
    }

    /**
     * @param  array<string, UploadedFile|null>  $files
     * @param  array<string, string>  $labels
     */
    private function storeArtworkFiles(CartItem $item, array $files, array $labels): void
    {
        foreach ($files as $slug => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('cart-artwork/'.$item->id, 'public');
            CartItemFile::create([
                'cart_item_id' => $item->id,
                'placement_slug' => $slug,
                'placement_label' => $labels[$slug] ?? ucwords(str_replace('_', ' ', $slug)),
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    /**
     * @param  array<string, int>  $libraryIds
     * @param  array<string, string>  $labels
     * @param  array<int, string>  $uploadSlugs  placements already covered by uploads
     */
    private function attachLibraryArtwork(CartItem $item, array $libraryIds, array $labels, array $uploadSlugs = []): void
    {
        if (! Auth::check() || $libraryIds === []) {
            return;
        }

        $disk = Storage::disk('public');
        $uploadLookup = array_fill_keys($uploadSlugs, true);

        foreach ($libraryIds as $slug => $artworkId) {
            if (isset($uploadLookup[$slug])) {
                continue;
            }

            $artwork = UserArtwork::query()
                ->where('user_id', Auth::id())
                ->where('id', (int) $artworkId)
                ->first();

            if (! $artwork || ! $disk->exists($artwork->path)) {
                continue;
            }

            $filename = basename($artwork->path);
            $destination = 'cart-artwork/'.$item->id.'/'.$filename;
            if ($disk->exists($destination)) {
                $destination = 'cart-artwork/'.$item->id.'/'.uniqid('', true).'_'.$filename;
            }
            $disk->copy($artwork->path, $destination);

            CartItemFile::create([
                'cart_item_id' => $item->id,
                'placement_slug' => (string) $slug,
                'placement_label' => $labels[$slug] ?? ucwords(str_replace('_', ' ', (string) $slug)),
                'path' => $destination,
                'original_name' => $artwork->original_name ?: $artwork->name,
            ]);
        }
    }
}
