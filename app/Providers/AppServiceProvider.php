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
    }

    public function boot(): void
    {
        View::composer(['partials.header', 'partials.footer', 'home'], function ($view) {
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

            $view->with([
                'catalogCategories' => $categories,
                'searchSuggestions' => $searchSuggestions,
            ]);

            if ($view->getName() === 'home') {
                $view->with([
                    'featuredProducts' => $catalog->productsForSearch()->take(3)->values()->all(),
                    'defaultCategorySlug' => $categories[0]['slug'] ?? 'workwear',
                ]);
            }
        });
    }
}
