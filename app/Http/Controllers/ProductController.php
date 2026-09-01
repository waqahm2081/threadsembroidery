<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductCatalogService $catalog,
    ) {
    }

    public function show(string $categorySlug, string $productSlug): View
    {
        $category = $this->catalog->resolveCategoryBySlug($categorySlug);
        abort_unless($category, 404);

        $summary = $this->catalog->resolveProduct($categorySlug, $productSlug);
        abort_unless($summary, 404);

        $product = $this->catalog->getProductDetail($summary['style_code'], $category['name']);
        abort_unless($product, 404);

        $related = $this->catalog->getRelatedProducts($category['name'], $product['style_code']);

        return view('shop.product', [
            'pageActive' => 'shop',
            'category' => $category,
            'product' => $product,
            'related' => $related,
        ]);
    }

    public function customizer(string $categorySlug, string $productSlug)
    {
        return redirect()->route('shop.product', [
            'categorySlug' => $categorySlug,
            'productSlug' => $productSlug,
        ]);
    }
}
