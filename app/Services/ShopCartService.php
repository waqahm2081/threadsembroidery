<?php

namespace App\Services;

use App\Models\PcProduct;
use App\Models\ShopCart;
use App\Models\ShopCartItem;
use Illuminate\Support\Facades\Auth;

class ShopCartService
{
    public function getCart(): ShopCart
    {
        if (Auth::check()) {
            return ShopCart::firstOrCreate(['user_id' => Auth::id()])->load('items');
        }

        return ShopCart::firstOrCreate(
            ['session_id' => session()->getId(), 'user_id' => null]
        )->load('items');
    }

    public function mergeGuestCartForUser(int $userId, string $guestSessionId): void
    {
        if ($guestSessionId === '') {
            return;
        }

        $userCart = ShopCart::firstOrCreate(['user_id' => $userId]);
        $guestCart = ShopCart::query()
            ->where('session_id', $guestSessionId)
            ->whereNull('user_id')
            ->first();

        if (! $guestCart || $guestCart->id === $userCart->id) {
            return;
        }

        $guestCart->items()->update(['shop_cart_id' => $userCart->id]);
        $guestCart->delete();
    }

    public function itemCount(): int
    {
        return $this->getCart()->itemCount();
    }

    public function addItem(array $data): ShopCartItem
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
            'logo_setup_fee' => ShopPricingService::logoSetupFee((bool) ($data['logo_setup'] ?? false)),
            'is_customized' => false,
        ]);
    }

    public function findItem(int $itemId): ?ShopCartItem
    {
        return $this->getCart()->items()->where('shop_cart_items.id', $itemId)->first();
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
