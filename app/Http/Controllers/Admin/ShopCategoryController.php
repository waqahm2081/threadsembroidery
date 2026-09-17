<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopCategorySetting;
use App\Services\ProductCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopCategoryController extends Controller
{
    public function __construct(private ProductCatalogService $catalog)
    {
    }

    public function index(Request $request): View
    {
        $this->syncFromCatalog();

        $q = trim((string) $request->query('q', ''));

        $categories = ShopCategorySetting::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('name', 'like', "%{$q}%")
                        ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(40)
            ->withQueryString();

        $counts = collect($this->catalog->getCategories())
            ->mapWithKeys(fn ($c) => [$c['slug'] => (int) ($c['count'] ?? 0)]);

        return view('admin.products-shop.categories.index', [
            'title' => 'Shop Categories',
            'subtitle' => 'Manage category VAT settings for the Products Shop.',
            'nav' => 'products-shop-categories',
            'categories' => $categories,
            'counts' => $counts,
            'q' => $q,
        ]);
    }

    public function edit(ShopCategorySetting $shopCategory): View
    {
        $catalog = collect($this->catalog->getCategories())
            ->firstWhere('slug', $shopCategory->slug);

        return view('admin.products-shop.categories.edit', [
            'title' => $shopCategory->name,
            'subtitle' => 'Category VAT settings',
            'nav' => 'products-shop-categories',
            'category' => $shopCategory,
            'productCount' => (int) ($catalog['count'] ?? 0),
        ]);
    }

    public function update(Request $request, ShopCategorySetting $shopCategory): RedirectResponse
    {
        $validated = $request->validate([
            'vat_enabled' => ['nullable'],
            'vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $shopCategory->update([
            'vat_enabled' => $request->boolean('vat_enabled'),
            'vat_percent' => isset($validated['vat_percent'])
                ? (float) $validated['vat_percent']
                : 20.0,
        ]);

        return redirect()
            ->route('admin.products-shop.categories.edit', $shopCategory)
            ->with('success', 'Category VAT settings saved.');
    }

    private function syncFromCatalog(): void
    {
        foreach ($this->catalog->getCategories() as $category) {
            ShopCategorySetting::query()->updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
