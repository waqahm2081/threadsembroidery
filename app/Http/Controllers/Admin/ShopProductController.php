<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PcProduct;
use App\Models\ShopCategorySetting;
use App\Services\ProductCatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ShopProductController extends Controller
{
    public function __construct(private ProductCatalogService $catalog)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('category', ''));

        $query = PcProduct::query()
            ->active()
            ->select([
                'style_code',
                DB::raw('MIN(title) as title'),
                DB::raw('MIN(brand) as brand'),
                DB::raw('MIN(type) as type'),
                DB::raw('MIN(price_single) as min_price'),
                DB::raw('MAX(price_single) as max_price'),
                DB::raw('COUNT(*) as variant_count'),
                DB::raw('MIN(id) as sample_id'),
            ])
            ->groupBy('style_code');

        if ($q !== '') {
            $query->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('style_code', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%");
            });
        }

        if ($categorySlug !== '') {
            $category = $this->catalog->resolveCategoryBySlug($categorySlug);
            if ($category) {
                $name = $category['name'];
                $query->where(function ($inner) use ($name) {
                    $inner->where('categories', 'like', '%'.$name.';%')
                        ->orWhere('categories', 'like', '%'.$name)
                        ->orWhere('categories', $name)
                        ->orWhere('categories', 'like', '%>'.$name.';%')
                        ->orWhere('categories', 'like', '%>'.$name);
                });
            }
        }

        $products = $query
            ->orderBy('title')
            ->paginate(30)
            ->withQueryString();

        $sampleIds = $products->pluck('sample_id')->filter()->all();
        $thumbs = [];
        if ($sampleIds !== []) {
            $rows = PcProduct::query()
                ->whereIn('id', $sampleIds)
                ->get(['id', 'image_front', 'image_model']);
            foreach ($rows as $row) {
                $thumbs[$row->id] = $this->catalog->imageUrl($row->image_front ?: $row->image_model);
            }
        }

        $categories = ShopCategorySetting::query()->orderBy('name')->get(['name', 'slug']);

        return view('admin.products-shop.products.index', [
            'title' => 'Shop Products',
            'subtitle' => 'View catalogue products and edit variation prices.',
            'nav' => 'products-shop-products',
            'products' => $products,
            'thumbs' => $thumbs,
            'categories' => $categories,
            'q' => $q,
            'categorySlug' => $categorySlug,
        ]);
    }

    public function edit(string $styleCode): View
    {
        $rows = PcProduct::query()
            ->active()
            ->where('style_code', $styleCode)
            ->orderBy('colourway_code')
            ->orderBy('size')
            ->get();

        abort_if($rows->isEmpty(), 404);

        $first = $rows->first();
        $colours = [];

        foreach ($rows as $row) {
            $code = $row->colourway_code ?: 'default';
            if (! isset($colours[$code])) {
                $colours[$code] = [
                    'code' => $code,
                    'name' => $row->colourway_name ?: $code,
                    'hex' => $this->normalizeHex($row->rgb),
                    'images' => [],
                    'sizes' => [],
                ];
            }

            foreach ([
                $row->image_model,
                $row->image_front,
                $row->image_back,
                $row->image_side,
                $row->image_detail,
            ] as $imagePath) {
                $url = $this->catalog->imageUrl($imagePath);
                if ($url && ! in_array($url, $colours[$code]['images'], true)) {
                    $colours[$code]['images'][] = $url;
                }
            }

            $colours[$code]['sizes'][] = $row;
        }

        return view('admin.products-shop.products.edit', [
            'title' => $first->title,
            'subtitle' => 'Style '.$first->style_code.' · prices only (images & colours are read-only)',
            'nav' => 'products-shop-products',
            'product' => $first,
            'colours' => array_values($colours),
            'categories' => $this->catalogCategoriesFor($first->categories),
        ]);
    }

    public function update(Request $request, string $styleCode): RedirectResponse
    {
        $validated = $request->validate([
            'prices' => ['required', 'array'],
            'prices.*.price_single' => ['required', 'numeric', 'min:0'],
        ]);

        $ids = array_map('intval', array_keys($validated['prices']));
        $owned = PcProduct::query()
            ->where('style_code', $styleCode)
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        foreach ($validated['prices'] as $id => $priceRow) {
            $id = (int) $id;
            if (! in_array($id, $owned, true)) {
                continue;
            }

            PcProduct::query()->where('id', $id)->update([
                'price_single' => (float) $priceRow['price_single'],
            ]);
        }

        $this->catalog->clearCategoryCache();

        return redirect()
            ->route('admin.products-shop.products.edit', $styleCode)
            ->with('success', 'Variation prices updated.');
    }

    /**
     * @return list<string>
     */
    private function catalogCategoriesFor(?string $categories): array
    {
        if (! $categories) {
            return [];
        }

        $names = [];
        foreach (explode(';', $categories) as $path) {
            $path = trim($path);
            if ($path === '') {
                continue;
            }
            $parts = array_values(array_filter(array_map('trim', explode('>', $path))));
            $leaf = $parts[count($parts) - 1] ?? null;
            if ($leaf) {
                $names[$leaf] = true;
            }
        }

        return array_keys($names);
    }

    private function normalizeHex(?string $rgb): string
    {
        $rgb = trim((string) $rgb);
        if ($rgb === '') {
            return '#cccccc';
        }
        if (! str_starts_with($rgb, '#')) {
            $rgb = '#'.$rgb;
        }

        return strtolower($rgb);
    }
}
