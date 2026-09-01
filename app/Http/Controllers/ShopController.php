<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(private ProductCatalogService $catalog)
    {
    }

    public function index(): RedirectResponse
    {
        $categories = $this->catalog->getCategories();
        $first = $categories[0] ?? null;

        abort_unless($first, 404);

        return redirect()->route('shop.category', $first['slug']);
    }

    public function category(string $categorySlug): View
    {
        $category = $this->catalog->resolveCategoryBySlug($categorySlug);

        abort_unless($category, 404);

        $products = $this->catalog->getCategoryProducts($category['name']);
        $filters = $this->catalog->getFilterOptionsFromProducts($products);

        return view('shop.category', [
            'pageActive' => 'shop',
            'category' => $category,
            'products' => $products,
            'filters' => $filters,
        ]);
    }
}
