<?php

namespace App\Http\Controllers;

use App\Models\BrandingEnquiry;
use App\Models\Order;
use App\Models\SchoolUniformOrder;
use App\Models\ShopOrder;
use App\Models\User;
use App\Models\UserArtwork;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PortalController extends Controller
{
    public function __construct(private CartService $cartService)
    {
    }

    public function show()
    {
        $user = Auth::user();

        return view('portal', [
            'pageTitle' => 'Client Portal | Threads Embroidery & Print',
            'pageDescription' => 'Your Threads client portal — orders, artwork and account.',
            'pageActive' => '',
            'pageStyles' => ['pages.css', 'portal.css', 'cart.css'],
            'pageScripts' => ['artwork-modal.js', 'stripe-checkout.js', 'portal.js'],
            'mount' => ['active' => '', 'onReady' => 'THREADS.initPortal'],
            'portalBootstrap' => $this->payload($user),
        ]);
    }

    public function data()
    {
        return response()->json($this->payload(Auth::user()));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name = $validated['name'];
        $user->company_name = $validated['company_name'] ?? null;
        $user->phone = $validated['phone'] ?? null;

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars/'.$user->id, 'public');
        }

        $user->save();

        return response()->json([
            'ok' => true,
            'message' => 'Profile updated.',
            'portal' => $this->payload($user->fresh()),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'ok' => true,
            'message' => 'Password updated.',
            'portal' => $this->payload($user->fresh()),
        ]);
    }

    public function storeArtwork(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'artwork' => ['required', 'array', 'min:1', 'max:20'],
            'artwork.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,gif,webp,svg,pdf'],
        ]);

        $created = [];
        foreach ($request->file('artwork', []) as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('user-artwork/'.$user->id, 'public');
            $original = $file->getClientOriginalName();

            $artwork = UserArtwork::create([
                'user_id' => $user->id,
                'name' => pathinfo($original, PATHINFO_FILENAME) ?: 'Artwork',
                'original_name' => $original,
                'path' => $path,
                'source_path' => null,
            ]);

            $created[] = $artwork->toFrontend();
        }

        $count = count($created);

        return response()->json([
            'ok' => true,
            'message' => $count === 1
                ? 'Artwork added to your library.'
                : "{$count} artworks added to your library.",
            'artwork' => $created,
            'portal' => $this->payload($user->fresh()),
        ]);
    }

    public function destroyArtwork(int $artworkId)
    {
        $user = Auth::user();
        $artwork = UserArtwork::query()
            ->where('user_id', $user->id)
            ->where('id', $artworkId)
            ->firstOrFail();

        if ($artwork->path) {
            Storage::disk('public')->delete($artwork->path);
        }
        $artwork->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Artwork deleted.',
            'portal' => $this->payload($user->fresh()),
        ]);
    }

    public function reorder(int $orderId)
    {
        $order = $this->findUserOrder($orderId)->load('items.files');
        $count = $this->cartService->cloneOrderItemsToCart($order);

        return response()->json([
            'ok' => true,
            'message' => $count ? 'Order items added to your cart.' : 'No items to reorder.',
            'redirect' => route('cart'),
            'cart' => $this->cartService->toFrontend(),
        ]);
    }

    public function order(int $orderId)
    {
        $order = $this->findUserOrder($orderId)->load('items.files');

        return response()->json([
            'ok' => true,
            'order' => $this->transformOrder($order),
        ]);
    }

    private function findUserOrder(int $orderId): Order
    {
        $user = Auth::user();

        return Order::query()
            ->where('id', $orderId)
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->firstOrFail();
    }

    private function payload($user): array
    {
        $orders = Order::query()
            ->with('items.files')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->latest()
            ->get();

        // Claim any email-matched guest orders
        Order::query()
            ->whereNull('user_id')
            ->where('email', $user->email)
            ->update(['user_id' => $user->id]);

        $this->syncOrderArtworkIntoLibrary($user, $orders);

        BrandingEnquiry::query()
            ->whereNull('user_id')
            ->where('email', $user->email)
            ->update(['user_id' => $user->id]);

        $enquiries = BrandingEnquiry::query()
            ->with('files')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->latest()
            ->get()
            ->map(fn (BrandingEnquiry $e) => $e->toFrontend())
            ->values()
            ->all();

        $artwork = $user->artworks()
            ->get()
            ->map(fn (UserArtwork $a) => $a->toFrontend())
            ->values()
            ->all();

        $transformed = $orders->map(fn (Order $o) => $this->transformOrder($o))->values()->all();
        $inProduction = collect($transformed)->whereIn('status_key', ['pending', 'processing', 'production'])->count();

        SchoolUniformOrder::query()
            ->whereNull('user_id')
            ->where('customer_email', $user->email)
            ->update(['user_id' => $user->id]);

        $uniformOrders = SchoolUniformOrder::query()
            ->with(['school', 'items'])
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('customer_email', $user->email);
            })
            ->latest()
            ->get()
            ->map(fn (SchoolUniformOrder $o) => $this->transformUniformOrder($o))
            ->values()
            ->all();

        ShopOrder::query()
            ->whereNull('user_id')
            ->where('email', $user->email)
            ->update(['user_id' => $user->id]);

        $shopOrders = ShopOrder::query()
            ->with('items')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->latest()
            ->get()
            ->map(fn (ShopOrder $o) => $this->transformShopOrder($o))
            ->values()
            ->all();

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_name' => $user->company_name,
                'phone' => $user->phone,
                'initials' => $user->initials() ?: 'U',
                'avatar_url' => $user->avatar_url,
            ],
            'orders' => $transformed,
            'uniform_orders' => $uniformOrders,
            'shop_orders' => $shopOrders,
            'enquiries' => $enquiries,
            'artwork' => $artwork,
            'stats' => [
                'orders' => count($transformed),
                'uniform_orders' => count($uniformOrders),
                'shop_orders' => count($shopOrders),
                'in_production' => $inProduction,
                'artwork' => count($artwork),
                'enquiries' => count($enquiries),
                'lifetime_spend' => round(
                    (float) $orders->sum('total')
                    + collect($uniformOrders)->sum('total')
                    + collect($shopOrders)->sum('total'),
                    2
                ),
            ],
            'routes' => [
                'bundles' => route('bundles'),
                'cart' => route('cart'),
                'shop' => route('shop'),
                'shop_cart' => route('shop.cart'),
                'logout' => route('logout'),
                'profile' => route('api.portal.profile'),
                'password' => route('api.portal.password'),
                'artwork' => route('api.portal.artwork.store'),
                'branding' => route('branding-enquiry'),
                'data' => route('api.portal.data'),
            ],
        ];
    }

    private function syncOrderArtworkIntoLibrary(User $user, $orders): void
    {
        $disk = Storage::disk('public');
        $existing = UserArtwork::query()
            ->where('user_id', $user->id)
            ->whereNotNull('source_path')
            ->pluck('source_path')
            ->all();
        $known = array_fill_keys($existing, true);

        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                foreach ($item->files as $file) {
                    if (! $file->path || isset($known[$file->path])) {
                        continue;
                    }
                    if (! $disk->exists($file->path)) {
                        continue;
                    }

                    $filename = basename($file->path);
                    $destination = 'user-artwork/'.$user->id.'/'.$filename;
                    if ($disk->exists($destination)) {
                        $destination = 'user-artwork/'.$user->id.'/'.uniqid('', true).'_'.$filename;
                    }
                    $disk->copy($file->path, $destination);

                    $original = $file->original_name ?: $filename;
                    UserArtwork::create([
                        'user_id' => $user->id,
                        'name' => pathinfo($original, PATHINFO_FILENAME) ?: 'Artwork',
                        'original_name' => $original,
                        'path' => $destination,
                        'source_path' => $file->path,
                    ]);

                    $known[$file->path] = true;
                }
            }
        }
    }

    private function transformOrder(Order $order): array
    {
        $statusKey = match ($order->status) {
            'pending', 'processing' => 'production',
            'shipped', 'dispatched' => 'dispatched',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            default => $order->status,
        };

        $statusLabel = match ($statusKey) {
            'production' => 'In Production',
            'dispatched' => 'Dispatched',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($order->status),
        };

        $firstItem = $order->items->first();

        return [
            'id' => $order->id,
            'ref' => $order->order_number,
            'status' => $statusLabel,
            'status_key' => $statusKey,
            'date' => optional($order->created_at)->format('d M Y'),
            'total' => (float) $order->total,
            'subtotal' => (float) $order->subtotal,
            'vat' => (float) $order->vat_amount,
            'delivery' => (float) $order->delivery_fee,
            'title' => $firstItem?->title ?? 'Bundle Order',
            'summary' => $firstItem?->summary ?? '',
            'item_count' => $order->items->count(),
            'garment_qty' => (int) $order->items->sum('quantity'),
            'method' => $firstItem?->method,
            'notes' => $order->notes,
            'shipping' => [
                'line1' => $order->shipping_address_line1,
                'line2' => $order->shipping_address_line2,
                'city' => $order->shipping_city,
                'postcode' => $order->shipping_postcode,
                'country' => $order->shipping_country,
            ],
            'billing_same_as_shipping' => (bool) $order->billing_same_as_shipping,
            'items' => $order->items->map(fn ($item) => [
                'title' => $item->title,
                'summary' => $item->summary,
                'method' => $item->method,
                'quantity' => $item->quantity,
                'total' => (float) $item->line_total,
                'colour' => $item->colour_name,
                'garments' => $item->garments_snapshot ?? [],
                'addons' => $item->addons ?? [],
                'sizes' => $item->size_breakdown ?? [],
                'files' => $item->files->map(fn ($f) => [
                    'label' => $f->placement_label,
                    'name' => $f->original_name,
                    'url' => asset('storage/'.$f->path),
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    private function transformUniformOrder(SchoolUniformOrder $order): array
    {
        $statusKey = match ($order->status) {
            'pending', 'processing' => 'production',
            'shipped' => 'dispatched',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            default => $order->status,
        };

        $statusLabel = match ($statusKey) {
            'production' => 'In Production',
            'dispatched' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => $order->statusLabel(),
        };

        $qty = (int) $order->items->sum('quantity');

        return [
            'id' => $order->id,
            'ref' => $order->order_number,
            'status' => $statusLabel,
            'status_key' => $statusKey,
            'date' => optional($order->created_at)->format('d M Y'),
            'total' => (float) $order->total,
            'subtotal' => (float) $order->subtotal,
            'vat' => (float) $order->vat_amount,
            'delivery' => (float) $order->delivery_fee,
            'school_name' => $order->school?->name,
            'item_count' => $order->items->count(),
            'garment_qty' => $qty,
            'notes' => $order->notes,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'company_name' => $order->company_name,
            'shipping' => [
                'line1' => $order->shipping_address_line1,
                'line2' => $order->shipping_address_line2,
                'city' => $order->shipping_city,
                'postcode' => $order->shipping_postcode,
                'country' => $order->shipping_country,
            ],
            'billing_same_as_shipping' => (bool) $order->billing_same_as_shipping,
            'billing' => $order->billing_same_as_shipping ? null : [
                'name' => $order->billing_name,
                'company' => $order->billing_company_name,
                'line1' => $order->billing_address_line1,
                'line2' => $order->billing_address_line2,
                'city' => $order->billing_city,
                'postcode' => $order->billing_postcode,
                'country' => $order->billing_country,
            ],
            'items' => $order->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total' => (float) $item->line_total,
            ])->values()->all(),
        ];
    }

    private function transformShopOrder(ShopOrder $order): array
    {
        $statusKey = match ($order->status) {
            'pending', 'processing' => 'production',
            'shipped' => 'dispatched',
            'delivered' => 'delivered',
            'cancelled' => 'cancelled',
            default => $order->status,
        };

        $statusLabel = match ($statusKey) {
            'production' => 'In Production',
            'dispatched' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($order->status),
        };

        $qty = (int) $order->items->sum('quantity');

        return [
            'id' => $order->id,
            'ref' => $order->order_number,
            'status' => $statusLabel,
            'status_key' => $statusKey,
            'date' => optional($order->created_at)->format('d M Y'),
            'total' => (float) $order->total,
            'subtotal' => (float) $order->subtotal,
            'vat' => (float) $order->vat_amount,
            'delivery' => 0.0,
            'item_count' => $order->items->count(),
            'garment_qty' => $qty,
            'notes' => $order->additional_instructions,
            'customer_name' => $order->name,
            'customer_email' => $order->email,
            'customer_phone' => $order->phone,
            'company_name' => $order->company_name,
            'shipping' => [
                'line1' => $order->shipping_address_line1,
                'city' => $order->shipping_city,
                'state' => $order->shipping_state,
                'postcode' => $order->shipping_zip,
            ],
            'billing_same_as_shipping' => (bool) $order->billing_same_as_shipping,
            'billing' => $order->billing_same_as_shipping ? null : [
                'company' => $order->billing_company_name,
                'line1' => $order->billing_address_line1,
                'city' => $order->billing_city,
                'state' => $order->billing_state,
                'postcode' => $order->billing_zip,
            ],
            'items' => $order->items->map(fn ($item) => [
                'product_title' => $item->product_title,
                'style_code' => $item->style_code,
                'colour_name' => $item->colour_name,
                'size' => $item->size,
                'size_breakdown' => $item->size_breakdown,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'decoration_fee' => (float) $item->decoration_fee,
                'logo_setup_fee' => (float) $item->logo_setup_fee,
                'total' => (float) $item->line_total,
            ])->values()->all(),
        ];
    }
}
