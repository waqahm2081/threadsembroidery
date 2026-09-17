<?php

namespace App\Support;

use Illuminate\Support\Str;

class OrderFulfillmentStatus
{
    public const PAID = 'paid';

    public const BEING_PREPARED = 'being_prepared';

    public const IN_PRODUCTION = 'in_production';

    public const READY_TO_PACK = 'ready_to_pack';

    public const DISPATCHED = 'dispatched';

    public const DELIVERED = 'delivered';

    public const CANCELLED = 'cancelled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_keys(self::labels());
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            self::PAID => 'Paid',
            self::BEING_PREPARED => 'Being Prepared',
            self::IN_PRODUCTION => 'In production',
            self::READY_TO_PACK => 'Ready to pack',
            self::DISPATCHED => 'Dispatched',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        ];
    }

    public static function label(?string $status): string
    {
        $status = (string) $status;

        return self::labels()[$status] ?? Str::title(str_replace('_', ' ', $status));
    }

    public static function requiresTracking(string $status): bool
    {
        return $status === self::DISPATCHED;
    }

    public static function badgeClass(?string $status): string
    {
        return match ((string) $status) {
            self::DELIVERED => 'green',
            self::CANCELLED => 'red',
            self::DISPATCHED, self::READY_TO_PACK, self::IN_PRODUCTION => 'blue',
            self::BEING_PREPARED, self::PAID => 'yellow',
            default => 'yellow',
        };
    }
}
