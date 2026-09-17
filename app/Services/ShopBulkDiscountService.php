<?php

namespace App\Services;

use App\Models\ShopProductBulkDiscount;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ShopBulkDiscountService
{
    /**
     * @return Collection<int, ShopProductBulkDiscount>
     */
    public function tiersForStyle(string $styleCode): Collection
    {
        return ShopProductBulkDiscount::query()
            ->where('style_code', $styleCode)
            ->orderBy('qty_from')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * @return list<array{qty_from:int,qty_to:int,discount_percent:float,label:string}>
     */
    public function tiersPayload(string $styleCode): array
    {
        return $this->tiersForStyle($styleCode)
            ->map(fn (ShopProductBulkDiscount $tier) => [
                'qty_from' => $tier->qty_from,
                'qty_to' => $tier->qty_to,
                'discount_percent' => (float) $tier->discount_percent,
                'label' => $tier->rangeLabel(),
            ])
            ->values()
            ->all();
    }

    public function percentForQty(string $styleCode, int $qty): float
    {
        if ($qty < 1) {
            return 0.0;
        }

        $tier = $this->tiersForStyle($styleCode)
            ->first(fn (ShopProductBulkDiscount $t) => $t->containsQty($qty));

        return $tier ? (float) $tier->discount_percent : 0.0;
    }

    public function applyPercent(float $unitPrice, float $percent): float
    {
        if ($percent <= 0) {
            return round($unitPrice, 2);
        }

        return round($unitPrice * (1 - ($percent / 100)), 2);
    }

    /**
     * Replace all tiers for a style. Rejects overlapping ranges.
     *
     * @param  list<array{qty_from:mixed,qty_to:mixed,discount_percent:mixed}>  $tiers
     */
    public function syncTiers(string $styleCode, array $tiers): void
    {
        $normalized = [];

        foreach ($tiers as $index => $tier) {
            $from = (int) ($tier['qty_from'] ?? 0);
            $to = (int) ($tier['qty_to'] ?? 0);
            $percent = (float) ($tier['discount_percent'] ?? 0);

            if ($from < 1 || $to < 1) {
                throw ValidationException::withMessages([
                    "tiers.{$index}.qty_from" => 'Quantity range must start at 1 or higher.',
                ]);
            }

            if ($to < $from) {
                throw ValidationException::withMessages([
                    "tiers.{$index}.qty_to" => '“Up to” quantity must be greater than or equal to “From”.',
                ]);
            }

            if ($percent < 0 || $percent > 100) {
                throw ValidationException::withMessages([
                    "tiers.{$index}.discount_percent" => 'Discount must be between 0 and 100%.',
                ]);
            }

            $normalized[] = [
                'qty_from' => $from,
                'qty_to' => $to,
                'discount_percent' => $percent,
                'sort_order' => $index,
            ];
        }

        usort($normalized, fn ($a, $b) => $a['qty_from'] <=> $b['qty_from']);

        for ($i = 0; $i < count($normalized); $i++) {
            for ($j = $i + 1; $j < count($normalized); $j++) {
                $a = $normalized[$i];
                $b = $normalized[$j];
                if ($a['qty_from'] <= $b['qty_to'] && $b['qty_from'] <= $a['qty_to']) {
                    throw ValidationException::withMessages([
                        'tiers' => 'Quantity ranges cannot overlap. Adjust the conflicting ranges and try again.',
                    ]);
                }
            }
        }

        ShopProductBulkDiscount::query()->where('style_code', $styleCode)->delete();

        foreach ($normalized as $row) {
            ShopProductBulkDiscount::query()->create([
                'style_code' => $styleCode,
                'qty_from' => $row['qty_from'],
                'qty_to' => $row['qty_to'],
                'discount_percent' => $row['discount_percent'],
                'sort_order' => $row['sort_order'],
            ]);
        }
    }
}
