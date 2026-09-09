<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function __construct(private ProductCatalogService $catalog)
    {
    }

    public function index(): View
    {
        $categories = $this->catalog->getVisibleCategories();

        abort_unless($categories !== [], 404);

        return view('shop.index', [
            'pageActive' => 'shop',
            'categories' => $categories,
        ]);
    }

    public function category(string $categorySlug): View
    {
        $category = $this->catalog->resolveCategoryBySlug($categorySlug);

        abort_unless($category, 404);

        $products = $this->catalog->getCategoryProducts($category['name']);
        $filters = $this->catalog->getFilterOptionsForCategory($category['name']);

        return view('shop.category', [
            'pageActive' => 'shop',
            'category' => $category,
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function categoryProducts(Request $request, string $categorySlug): JsonResponse
    {
        $category = $this->catalog->resolveCategoryBySlug($categorySlug);

        abort_unless($category, 404);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = max(1, min(50, (int) $request->query('per_page', 25)));

        $result = $this->catalog->getCategoryProductsPaginated($category['name'], $page, $perPage);

        return response()->json([
            'products' => $result['products']->values()->all(),
            'total' => $result['total'],
            'page' => $result['page'],
            'per_page' => $result['per_page'],
            'has_more' => $result['has_more'],
        ]);
    }
}
