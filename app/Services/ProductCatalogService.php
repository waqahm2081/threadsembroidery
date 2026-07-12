<?php

namespace App\Services;

use App\Models\PcProduct;
use App\Models\PcProductCustomizerImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductCatalogService
{
    /** @var array<string, array{name:string,slug:string,count:int}>|null */
    private ?array $categoryIndex = null;

    public function getCategories(): array
    {
        return array_values($this->buildCategoryIndex());
    }

    public function resolveCategoryBySlug(string $slug): ?array
    {
        return $this->buildCategoryIndex()[$slug] ?? null;
    }

    public function categorySlug(string $name): string
    {
        return Str::slug($name);
    }

    public function productSlug(string $title, string $styleCode): string
    {
        return Str::slug($title . ' ' . Str::lower($styleCode));
    }

    public function parseCategoryNames(?string $categories): array
    {
        if (!$categories) {
            return [];
        }

        $names = [];
        foreach (explode(';', $categories) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $name = str_contains($part, '>') ? trim(substr($part, strrpos($part, '>') + 1)) : $part;
            if ($name !== '') {
                $names[] = $name;
            }
        }

        return array_values(array_unique($names));
    }

    public function productMatchesCategory(PcProduct $row, string $categoryName): bool
    {
        return in_array($categoryName, $this->parseCategoryNames($row->categories), true);
    }

    public function getCategoryProducts(string $categoryName): Collection
    {
        $rows = PcProduct::active()
            ->whereNotNull('categories')
            ->orderBy('title')
            ->orderBy('colourway_code')
            ->orderBy('size')
            ->get()
            ->filter(fn (PcProduct $row) => $this->productMatchesCategory($row, $categoryName));

        return $this->groupRowsByStyle($rows, $categoryName);
    }

    public function resolveProduct(string $categorySlug, string $productSlug): ?array
    {
        $category = $this->resolveCategoryBySlug($categorySlug);
        if (!$category) {
            return null;
        }

        foreach ($this->getCategoryProducts($category['name']) as $product) {
            if ($product['slug'] === $productSlug) {
                return $product;
            }
        }

        return null;
    }

    public function getProductDetail(string $styleCode, string $categoryName): ?array
    {
        $rows = PcProduct::active()
            ->where('style_code', $styleCode)
            ->orderBy('colourway_code')
            ->orderBy('size')
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        $first = $rows->first();
        if (!$this->productMatchesCategory($first, $categoryName)) {
            return null;
        }

        $colours = [];
        foreach ($rows as $row) {
            $code = $row->colourway_code ?: 'default';

            if (!isset($colours[$code])) {
                $colours[$code] = [
                    'code' => $code,
                    'name' => $row->colourway_name ?: $code,
                    'hex' => $this->normalizeHex($row->rgb),
                    'images' => array_values(array_filter([
                        $row->image_model,
                        $row->image_front,
                        $row->image_back,
                        $row->image_side,
                        $row->image_detail,
                    ])),
                    'sizes' => [],
                ];
            }

            $colours[$code]['sizes'][] = [
                'sku' => $row->sku,
                'size' => $row->size,
                'stock' => (int) $row->stock_fts,
                'price_single' => (float) $row->price_single,
                'price_pack' => (float) $row->price_pack,
                'pack_qty' => (int) $row->pack_qty,
                'price_carton' => (float) $row->price_carton,
                'carton_qty' => (int) $row->carton_qty,
                'conversions' => $row->size_conversions,
            ];
        }

        $colourList = array_values($colours);
        $minPrice = $rows->min('price_single');
        $attributes = $this->parseAttributes($first->attributes);
        $representativeId = (int) $rows->min('id');
        $customizerImage = $this->getCustomizerImagePath($representativeId);

        return [
            'style_code' => $first->style_code,
            'title' => $first->title,
            'slug' => $this->productSlug($first->title, $first->style_code),
            'body' => $first->body,
            'brand' => $first->brand,
            'gender' => $first->gender,
            'type' => $first->type,
            'material' => $first->material,
            'weight' => $first->weight,
            'category_name' => $categoryName,
            'category_slug' => $this->categorySlug($categoryName),
            'categories' => $this->parseCategoryNames($first->categories),
            'collections' => $this->splitList($first->collections),
            'attributes' => $attributes,
            'is_new' => (bool) $rows->contains(fn (PcProduct $row) => $row->is_new),
            'is_clearance' => (bool) $rows->contains(fn (PcProduct $row) => $row->is_clearance),
            'min_price' => (float) $minPrice,
            'colours' => $colourList,
            'customizer_image' => $customizerImage,
            'representative_id' => $representativeId,
        ];
    }

    public function getFilterOptions(string $categoryName): array
    {
        $products = $this->getCategoryProducts($categoryName);

        $brands = $products->pluck('brand')->filter()->unique()->sort()->values()->all();
        $types = $products->pluck('type')->filter()->unique()->sort()->values()->all();

        $colours = [];
        foreach ($products as $product) {
            foreach ($product['colours'] as $colour) {
                $colours[$colour['name']] = [
                    'name' => $colour['name'],
                    'hex' => $colour['hex'],
                ];
            }
        }

        $prices = $products->pluck('min_price')->filter();
        $minPrice = $prices->min() ?? 0;
        $maxPrice = $prices->max() ?? 100;
        $maxPrice = (int) ceil($maxPrice);

        return [
            'brands' => $brands,
            'types' => $types,
            'colours' => array_values($colours),
            'price_min' => floor($minPrice),
            'price_max' => max($maxPrice, (int) floor($minPrice) + 1),
        ];
    }

    public function getRelatedProducts(string $categoryName, string $excludeStyleCode, int $limit = 4): Collection
    {
        return $this->getCategoryProducts($categoryName)
            ->reject(fn (array $product) => $product['style_code'] === $excludeStyleCode)
            ->take($limit)
            ->values();
    }

    public function getCustomizerImagePath(int $productId): string
    {
        $record = PcProductCustomizerImage::where('product_id', $productId)->first();

        if ($record && $record->image) {
            return $this->resolveImageUrl($record->image);
        }

        return asset('assets/images/customizer/tshirtcustom.png');
    }

    public function productsForSearch(): Collection
    {
        return $this->groupRowsByStyle(
            PcProduct::active()->orderBy('title')->get()
        );
    }

    private function buildCategoryIndex(): array
    {
        if ($this->categoryIndex !== null) {
            return $this->categoryIndex;
        }

        $index = [];
        $rows = PcProduct::active()->whereNotNull('categories')->get(['categories', 'style_code']);

        foreach ($rows as $row) {
            foreach ($this->parseCategoryNames($row->categories) as $name) {
                $slug = $this->categorySlug($name);
                if (!isset($index[$slug])) {
                    $index[$slug] = [
                        'name' => $name,
                        'slug' => $slug,
                        'count' => 0,
                        'style_codes' => [],
                    ];
                }

                if (!isset($index[$slug]['style_codes'][$row->style_code])) {
                    $index[$slug]['style_codes'][$row->style_code] = true;
                    $index[$slug]['count']++;
                }
            }
        }

        foreach ($index as &$category) {
            unset($category['style_codes']);
        }
        unset($category);

        uasort($index, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $this->categoryIndex = $index;
    }

    private function groupRowsByStyle(Collection $rows, ?string $categoryName = null): Collection
    {
        $groups = [];

        foreach ($rows as $row) {
            $styleCode = $row->style_code;

            if (!isset($groups[$styleCode])) {
                $groups[$styleCode] = [
                    'style_code' => $styleCode,
                    'title' => $row->title,
                    'slug' => $this->productSlug($row->title, $styleCode),
                    'brand' => $row->brand,
                    'type' => $row->type,
                    'category_name' => $categoryName ?? ($this->parseCategoryNames($row->categories)[0] ?? ''),
                    'category_slug' => $categoryName ? $this->categorySlug($categoryName) : $this->categorySlug($this->parseCategoryNames($row->categories)[0] ?? 'shop'),
                    'min_price' => (float) $row->price_single,
                    'max_price' => (float) $row->price_single,
                    'image' => $this->resolveImageUrl($row->image_front ?: $row->image_model),
                    'is_new' => (bool) $row->is_new,
                    'is_clearance' => (bool) $row->is_clearance,
                    'colours' => [],
                    'colour_names' => [],
                    'url' => null,
                ];
            }

            $groups[$styleCode]['min_price'] = min($groups[$styleCode]['min_price'], (float) $row->price_single);
            $groups[$styleCode]['max_price'] = max($groups[$styleCode]['max_price'], (float) $row->price_single);
            $groups[$styleCode]['is_new'] = $groups[$styleCode]['is_new'] || (bool) $row->is_new;
            $groups[$styleCode]['is_clearance'] = $groups[$styleCode]['is_clearance'] || (bool) $row->is_clearance;

            $colourName = $row->colourway_name ?: $row->colourway_code;
            if ($colourName && !in_array($colourName, $groups[$styleCode]['colour_names'], true)) {
                $groups[$styleCode]['colour_names'][] = $colourName;
                $groups[$styleCode]['colours'][] = [
                    'name' => $colourName,
                    'hex' => $this->normalizeHex($row->rgb),
                ];
            }
        }

        return collect($groups)
            ->map(function (array $product) {
                $product['min_price'] = PricingService::displayUnitPrice($product['min_price']);
                $product['max_price'] = PricingService::displayUnitPrice($product['max_price']);
                $product['url'] = route('shop.product', [
                    'categorySlug' => $product['category_slug'],
                    'productSlug' => $product['slug'],
                ]);

                return $product;
            })
            ->values();
    }

    private function parseAttributes(?string $attributes): array
    {
        if (!$attributes) {
            return [];
        }

        $parsed = [];
        foreach (explode(';', $attributes) as $pair) {
            $pair = trim($pair);
            if ($pair === '') {
                continue;
            }

            if (str_contains($pair, ':')) {
                [$key, $value] = explode(':', $pair, 2);
                $parsed[trim($key)] = trim($value);
            } else {
                $parsed[] = $pair;
            }
        }

        return $parsed;
    }

    private function splitList(?string $value): array
    {
        if (!$value) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(';', $value))));
    }

    private function normalizeHex(?string $rgb): string
    {
        $rgb = trim((string) $rgb);
        if ($rgb === '') {
            return '#cccccc';
        }

        if (!str_starts_with($rgb, '#')) {
            $rgb = '#' . $rgb;
        }

        return strtolower($rgb);
    }

    private function resolveImageUrl(?string $path): string
    {
        if (!$path) {
            return asset('assets/images/prod1.jpg');
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
