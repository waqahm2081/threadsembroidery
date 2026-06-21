<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(private ProductCatalogService $catalog)
    {
    }

    public function category(string $categorySlug): View
    {
        $category = $this->catalog->resolveCategoryBySlug($categorySlug);

        abort_unless($category, 404);

        $products = $this->catalog->getCategoryProducts($category['name']);
        $filters = $this->catalog->getFilterOptions($category['name']);

        return view('shop.category', [
            'category' => $category,
            'products' => $products,
            'filters' => $filters,
        ]);
    }
}
