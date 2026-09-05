<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use App\Services\ShopCartService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductCatalogService $catalog,
        private ShopCartService $cartService,
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

        $decoration = \App\Services\ShopDecorationRulesService::forProduct($product, $category['name']);

        return view('shop.product', [
            'pageActive' => 'shop',
            'category' => $category,
            'product' => $product,
            'related' => $related,
            'decoration' => $decoration,
        ]);
    }

    public function customizer(string $categorySlug, string $productSlug): View
    {
        $category = $this->catalog->resolveCategoryBySlug($categorySlug);
        abort_unless($category, 404);

        $summary = $this->catalog->resolveProduct($categorySlug, $productSlug);
        abort_unless($summary, 404);

        $product = $this->catalog->getProductDetail($summary['style_code'], $category['name']);
        abort_unless($product, 404);

        $cartItem = null;
        if ($cartItemId = request('cart_item_id')) {
            $cartItem = $this->cartService->findItem((int) $cartItemId);
        }

        $decoration = \App\Services\ShopDecorationRulesService::forProduct($product, $category['name']);

        return view('shop.customizer', [
            'pageActive' => 'shop',
            'category' => $category,
            'product' => $product,
            'cartItem' => $cartItem,
            'decoration' => $decoration,
        ]);
    }
}
