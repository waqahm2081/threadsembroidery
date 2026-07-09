<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\PcProduct;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()])->load('items');
        }

        return Cart::firstOrCreate(
            ['session_id' => session()->getId(), 'user_id' => null]
        )->load('items');
    }

    /**
     * Move guest cart items into the authenticated user's cart.
     * Must be called with the session ID from *before* session()->regenerate().
     */
    public function mergeGuestCartForUser(int $userId, string $guestSessionId): void
    {
        if ($guestSessionId === '') {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        $guestCart = Cart::where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->first();

        if (! $guestCart || $guestCart->id === $userCart->id) {
            return;
        }

        $guestCart->items()->update(['cart_id' => $userCart->id]);
        $guestCart->delete();
    }

    public function itemCount(): int
    {
        return $this->getCart()->itemCount();
    }

    public function addItem(array $data): CartItem
    {
        $cart = $this->getCart();

        $productId = $this->resolveProductId(
            $data['style_code'],
            $data['colour_code'] ?? null,
            $data['size'] ?? null
        );

        return $cart->items()->create([
            'product_id' => $productId,
            'style_code' => $data['style_code'],
            'product_title' => $data['product_title'],
            'category_slug' => $data['category_slug'],
            'product_slug' => $data['product_slug'],
            'colour_code' => $data['colour_code'] ?? null,
            'colour_name' => $data['colour_name'] ?? null,
            'colour_hex' => $data['colour_hex'] ?? null,
            'size' => $data['size'] ?? null,
            'size_breakdown' => $data['size_breakdown'] ?? null,
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'decoration_fee' => $data['decoration_fee'] ?? 0,
            'logo_setup_fee' => PricingService::logoSetupFee((bool) ($data['logo_setup'] ?? false)),
            'is_customized' => false,
        ]);
    }

    public function findItem(int $itemId): ?CartItem
    {
        return $this->getCart()->items()->where('cart_items.id', $itemId)->first();
    }

    public function removeItem(int $itemId): bool
    {
        $item = $this->findItem($itemId);

        return $item ? (bool) $item->delete() : false;
    }

    public function clear(): void
    {
        $this->getCart()->items()->delete();
    }

    private function resolveProductId(string $styleCode, ?string $colourCode, ?string $size): ?int
    {
        $query = PcProduct::active()->where('style_code', $styleCode);

        if ($colourCode) {
            $query->where('colourway_code', $colourCode);
        }

        if ($size) {
            $query->where('size', $size);
        }

        return $query->value('id');
    }
}
