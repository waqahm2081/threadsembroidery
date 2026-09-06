<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\PlacementOption;
use App\Models\SizeGuide;

class BundleCatalogService
{
    public function frontendPayload(): array
    {
        $guides = SizeGuide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $sizeGuides = [];
        foreach ($guides as $guide) {
            $sizeGuides[$guide->key] = $guide->toFrontend();
        }

        $bundles = Bundle::query()
            ->where('is_active', true)
            ->with(['includes', 'garments.colours'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Bundle $b) => $b->toFrontend())
            ->values()
            ->all();

        $placements = PlacementOption::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (PlacementOption $p) => $p->toFrontend())
            ->values()
            ->all();

        $colours = \App\Models\BundleColour::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => $c->toFrontend())
            ->values()
            ->all();

        // Fallback palette if colours table is empty (pre-seed)
        if ($colours === []) {
            $colours = [
                ['name' => 'Black', 'hex' => '#1c1c1c'],
                ['name' => 'Navy', 'hex' => '#1f2a44'],
                ['name' => 'White', 'hex' => '#f4f4f2'],
            ];
        }

        return [
            'bundles' => $bundles,
            'sizeGuides' => $sizeGuides,
            'placements' => $placements,
            'colours' => $colours,
            'sizeScale' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            'vatRate' => 0.20,
            'freeDeliveryOver' => 150,
            'deliveryFee' => 6.95,
        ];
    }
}
