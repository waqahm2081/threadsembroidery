<?php

namespace App\Support;

use App\Models\School;

/**
 * Shared uniform-shop pricing helpers (VAT per school + delivery rules).
 */
class UniformPricing
{
    public const DELIVERY_FEE = 4.99;

    public const FREE_DELIVERY_THRESHOLD = 50.0;

    public const DEFAULT_VAT_PERCENT = 20.0;

    public static function deliveryFee(float $subtotal): float
    {
        return $subtotal >= self::FREE_DELIVERY_THRESHOLD ? 0.0 : self::DELIVERY_FEE;
    }

    public static function vatAmount(float $subtotal, ?School $school): float
    {
        if (! $school || ! $school->vat_enabled) {
            return 0.0;
        }

        $percent = (float) ($school->vat_percent ?? self::DEFAULT_VAT_PERCENT);
        if ($percent <= 0) {
            return 0.0;
        }

        return round($subtotal * ($percent / 100), 2);
    }

    public static function total(float $subtotal, ?School $school): array
    {
        $subtotal = round($subtotal, 2);
        $vat = self::vatAmount($subtotal, $school);
        $delivery = self::deliveryFee($subtotal);
        $total = round($subtotal + $vat + $delivery, 2);

        return [
            'subtotal' => $subtotal,
            'vat' => $vat,
            'delivery' => $delivery,
            'total' => $total,
            'vat_enabled' => (bool) ($school?->vat_enabled),
            'vat_percent' => (float) ($school?->vat_percent ?? self::DEFAULT_VAT_PERCENT),
        ];
    }
}
