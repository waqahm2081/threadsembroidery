<?php

namespace App\Services;

use App\Models\ShopCart;
use App\Models\ShopCartItem;
use App\Models\ShopCategorySetting;

class ShopPricingService
{
    /** Fallback rate only when explicitly requested; cart VAT is category-driven. */
    public const VAT_RATE = 0.20;

    public const LOGO_SETUP_FEE = 12.00;

    public const PLACEMENT_FEES = [
        'left_chest' => 3.50,
        'right_chest' => 3.50,
        'front_print' => 3.50,
        'front_embroidery' => 3.50,
        'back_print' => 6.00,
        'back_embroidery' => 8.00,
        'apron_left' => 3.50,
        'apron_right' => 3.50,
        'apron_middle' => 3.50,
        'rucksack_top_print' => 6.00,
        'rucksack_top_embroidery' => 3.50,
        'bag_print' => 6.00,
        'keyring_print' => 6.00,
        'towel_embroidery' => 3.50,
        'full_print' => 6.00,
    ];

    public const DISPLAY_INCLUDED_PLACEMENT = 'left_chest';

    public static function displayIncludedDecorationFee(): float
    {
        return self::placementFee(self::DISPLAY_INCLUDED_PLACEMENT);
    }

    public static function displayUnitPrice(float $baseUnitPrice): float
    {
        return round($baseUnitPrice + self::displayIncludedDecorationFee(), 2);
    }

    public static function vatPercent(): int
    {
        return (int) round(self::VAT_RATE * 100);
    }

    /**
     * Effective VAT rate for a cart: max percent among VAT-enabled categories
     * represented in the cart. Zero when none apply.
     */
    public static function cartVatRate(ShopCart $cart): float
    {
        $slugs = $cart->items
            ->pluck('category_slug')
            ->filter(fn ($s) => is_string($s) && $s !== '')
            ->unique()
            ->values()
            ->all();

        if ($slugs === []) {
            return 0.0;
        }

        $maxPercent = ShopCategorySetting::query()
            ->whereIn('slug', $slugs)
            ->where('vat_enabled', true)
            ->max('vat_percent');

        if ($maxPercent === null || (float) $maxPercent <= 0) {
            return 0.0;
        }

        return ((float) $maxPercent) / 100;
    }

    public static function cartVatPercent(ShopCart $cart): float
    {
        return round(self::cartVatRate($cart) * 100, 2);
    }

    public static function placementFee(?string $placement): float
    {
        if (! $placement) {
            return 0.0;
        }

        return (float) (self::PLACEMENT_FEES[$placement] ?? 0);
    }

    public static function logoSetupFee(bool $selected): float
    {
        return $selected ? self::LOGO_SETUP_FEE : 0.0;
    }

    public static function itemLineSubtotal(ShopCartItem $item): float
    {
        return (($item->unit_price + $item->decoration_fee) * $item->quantity) + $item->logo_setup_fee;
    }

    public static function cartSubtotalExVat(ShopCart $cart): float
    {
        return (float) $cart->items->sum(fn (ShopCartItem $item) => self::itemLineSubtotal($item));
    }

    public static function vatAmount(float $subtotalExVat, ?float $rate = null): float
    {
        $rate ??= self::VAT_RATE;

        if ($rate <= 0) {
            return 0.0;
        }

        return round($subtotalExVat * $rate, 2);
    }

    public static function totalIncVat(float $subtotalExVat, ?float $rate = null): float
    {
        return round($subtotalExVat + self::vatAmount($subtotalExVat, $rate), 2);
    }

    /**
     * @return array<string, float|array<string, float>>
     */
    public static function clientConfig(): array
    {
        return [
            'vat_rate' => self::VAT_RATE,
            'logo_setup_fee' => self::LOGO_SETUP_FEE,
            'placement_fees' => self::PLACEMENT_FEES,
            'display_included_decoration' => self::displayIncludedDecorationFee(),
        ];
    }
}
