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
            ->with(['includes', 'garments'])
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

        return [
            'bundles' => $bundles,
            'sizeGuides' => $sizeGuides,
            'placements' => $placements,
            'colours' => [
                ['name' => 'Black', 'hex' => '#1c1c1c'],
                ['name' => 'Navy', 'hex' => '#1f2a44'],
                ['name' => 'White', 'hex' => '#f4f4f2'],
                ['name' => 'Red', 'hex' => '#b4232a'],
                ['name' => 'Royal Blue', 'hex' => '#1f5fd0'],
                ['name' => 'Bottle Green', 'hex' => '#12432c'],
                ['name' => 'Grey', 'hex' => '#6b6f76'],
                ['name' => 'Purple', 'hex' => '#5b2a86'],
            ],
            'sizeScale' => ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL'],
            'vatRate' => 0.20,
            'freeDeliveryOver' => 150,
            'deliveryFee' => 6.95,
        ];
    }
}
