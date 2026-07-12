<?php

namespace App\Providers;

use App\Services\ProductCatalogService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ProductCatalogService::class);
        $this->app->singleton(\App\Services\CartService::class);
    }

    public function boot(): void
    {
        View::composer(['partials.header', 'partials.footer', 'home', 'homeold', 'cart', 'checkout'], function ($view) {
            $catalog = app(ProductCatalogService::class);
            $categories = $catalog->getCategories();

            $searchSuggestions = collect($categories)->map(fn (array $category) => [
                'type' => 'category',
                'label' => $category['name'],
                'href' => route('shop.category', $category['slug']),
            ])->merge(
                $catalog->productsForSearch()->take(24)->map(fn (array $product) => [
                    'type' => 'product',
                    'label' => $product['title'],
                    'href' => $product['url'],
                ])
            )->values()->all();

            $cartCount = 0;
            try {
                $cartCount = app(\App\Services\CartService::class)->itemCount();
            } catch (\Throwable) {
                // Database may not be migrated yet
            }

            $view->with([
                'catalogCategories' => $categories,
                'searchSuggestions' => $searchSuggestions,
                'cartCount' => $cartCount,
            ]);

            if (in_array($view->getName(), ['home', 'homeold'], true)) {
                $view->with([
                    'featuredProducts' => $catalog->productsForSearch()->take(3)->values()->all(),
                    'defaultCategorySlug' => $categories[0]['slug'] ?? 'workwear',
                ]);
            }
        });
    }
}
