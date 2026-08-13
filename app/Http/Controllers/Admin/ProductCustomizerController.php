<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlacementOption;
use Illuminate\View\View;

class ProductCustomizerController extends Controller
{
    public function __invoke(): View
    {
        $placements = PlacementOption::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (PlacementOption $p) => $p->toFrontend())
            ->values()
            ->all();

        return view('admin.product-customizer.index', [
            'title' => 'Product Customizer',
            'subtitle' => 'Build a live garment spec — colour, branding, placements, logo and sizes.',
            'nav' => 'customizer',
            'cfgAdmin' => true,
            'placements' => $placements,
        ]);
    }
}
