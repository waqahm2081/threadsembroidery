<?php

namespace App\Services;

use App\Models\PcProduct;
use App\Models\PcProductCustomizerImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProductCatalogService
{
    private const STYLE_CHUNK = 100;

    /**
     * Category names hidden from shop nav / index only.
     * Products remain available when they also belong to visible categories,
     * and these category URLs still resolve if visited directly.
     *
     * @var list<string>
     */
    private const HIDDEN_CATEGORY_NAMES = [
        'Fishing',
        'Motorsport',
        'Football',
        'Sailing',
        'Running',
        'Racquet Sports',
        'Health, Salon & Beauty',
        'Equestrian',
        'Golf',
        'Darts & Pool',
        'Dance',
        'Coveralls',
        'Decoration Supplies',
        'Just Hoods',
        'Organic & Recycled',
        'Outdoor Leisure',
        'Security',
        'Winter Sports',
    ];

    /** @var array<string, array{name:string,slug:string,count:int,style_codes:array<string,bool>}>|null */
    private ?array $categoryIndex = null;

    public function getCategories(): array
    {
        return array_values(array_map(function (array $category) {
            unset($category['style_codes']);

            return $category;
        }, $this->buildCategoryIndex()));
    }

    /**
     * Categories shown in the header mega menu and main shop page.
     *
     * @return list<array{name:string,slug:string,count:int,image:?string,icon:string}>
     */
    public function getVisibleCategories(): array
    {
        $hiddenSlugs = array_fill_keys(
            array_map(fn (string $name) => $this->categorySlug($name), self::HIDDEN_CATEGORY_NAMES),
            true
        );

        $visible = [];
        $styleBySlug = [];

        foreach ($this->buildCategoryIndex() as $slug => $category) {
            if (isset($hiddenSlugs[$slug])) {
                continue;
            }

            $styleCodes = array_keys($category['style_codes'] ?? []);
            $firstStyle = $styleCodes[0] ?? null;
            if ($firstStyle) {
                $styleBySlug[$slug] = $firstStyle;
            }

            $visible[$slug] = [
                'name' => $category['name'],
                'slug' => $category['slug'],
                'count' => (int) $category['count'],
                'image' => null,
                'icon' => $this->categoryIcon($category['name']),
            ];
        }

        if ($styleBySlug !== []) {
            $images = $this->thumbnailsForStyleCodes(array_values(array_unique(array_values($styleBySlug))));
            foreach ($styleBySlug as $slug => $styleCode) {
                $visible[$slug]['image'] = $images[$styleCode] ?? null;
            }
        }

        return array_values($visible);
    }

    /**
     * @param  list<string>  $styleCodes
     * @return array<string, string>
     */
    private function thumbnailsForStyleCodes(array $styleCodes): array
    {
        if ($styleCodes === []) {
            return [];
        }

        $map = [];

        foreach (array_chunk($styleCodes, self::STYLE_CHUNK) as $chunk) {
            $rows = PcProduct::active()
                ->whereIn('style_code', $chunk)
                ->where(function ($q) {
                    $q->whereNotNull('image_front')->where('image_front', '!=', '')
                        ->orWhere(function ($q2) {
                            $q2->whereNotNull('image_model')->where('image_model', '!=', '');
                        });
                })
                ->selectRaw('style_code, MIN(id) as id')
                ->groupBy('style_code')
                ->pluck('id');

            if ($rows->isEmpty()) {
                continue;
            }

            foreach (
                PcProduct::query()
                    ->whereIn('id', $rows)
                    ->get(['style_code', 'image_front', 'image_model']) as $row
            ) {
                $url = $this->resolveOptionalImageUrl($row->image_front ?: $row->image_model);
                if ($url) {
                    $map[$row->style_code] = $url;
                }
            }
        }

        return $map;
    }

    private function categoryIcon(string $name): string
    {
        $slug = $this->categorySlug($name);

        $map = [
            'accessories' => 'bi-watch',
            'aprons' => 'bi-person-workspace',
            'bags' => 'bi-bag',
            'chefswear-catering' => 'bi-cup-hot',
            'children' => 'bi-emoji-smile',
            'cleaning' => 'bi-droplet',
            'college-uni' => 'bi-mortarboard',
            'corporatewear' => 'bi-briefcase',
            'fleece' => 'bi-clouds',
            'gilets-bodywarmers' => 'bi-person',
            'headwear' => 'bi-circle',
            'hi-vis' => 'bi-brightness-high',
            'hoods' => 'bi-person-bounding-box',
            'hospitality' => 'bi-cup-straw',
            'jackets' => 'bi-cloud-rain',
            'knitwear' => 'bi-grid-3x3',
            'lounge-sleepwear' => 'bi-moon-stars',
            'performance-activewear' => 'bi-lightning-charge',
            'polos' => 'bi-tag',
            'promotional-accessories' => 'bi-gift',
            'safetywear-ppe' => 'bi-shield-check',
            'schoolwear' => 'bi-book',
            'soft-shell' => 'bi-layers',
            'sports' => 'bi-trophy',
            'sweatshirts' => 'bi-heart',
            't-shirts' => 'bi-square',
            'towels-robes-slippers' => 'bi-water',
            'track-field' => 'bi-stopwatch',
            'trousers' => 'bi-distribute-vertical',
            'workwear' => 'bi-tools',
        ];

        return $map[$slug] ?? 'bi-grid';
    }

    public function resolveCategoryBySlug(string $slug): ?array
    {
        $category = $this->buildCategoryIndex()[$slug] ?? null;

        if (! $category) {
            return null;
        }

        unset($category['style_codes']);

        return $category;
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
        if (! $categories) {
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

    /**
     * @return array{products: Collection, total: int, page: int, per_page: int, has_more: bool}
     */
    public function getCategoryProductsPaginated(string $categoryName, int $page = 1, int $perPage = 25): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(50, $perPage));
        $all = $this->getCategoryProducts($categoryName);
        $total = $all->count();

        return [
            'products' => $all->slice(($page - 1) * $perPage, $perPage)->values(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'has_more' => ($page * $perPage) < $total,
        ];
    }

    public function getCategoryProducts(string $categoryName): Collection
    {
        $styleCodes = $this->getCategoryStyleCodes($categoryName);

        if ($styleCodes === []) {
            return collect();
        }

        return $this->buildSummariesForStyleCodes($styleCodes, $categoryName);
    }

    public function resolveProduct(string $categorySlug, string $productSlug): ?array
    {
        $category = $this->resolveCategoryBySlug($categorySlug);
        if (! $category) {
            return null;
        }

        $styleCode = $this->resolveStyleCodeBySlug($category['name'], $productSlug);
        if (! $styleCode) {
            return null;
        }

        $summaries = $this->buildSummariesForStyleCodes([$styleCode], $category['name']);
        $product = $summaries->first();

        return $product ?: null;
    }

    public function getProductDetail(string $styleCode, string $categoryName): ?array
    {
        $rows = PcProduct::active()
            ->where('style_code', $styleCode)
            ->orderBy('colourway_code')
            ->orderBy('size')
            ->get([
                'id', 'style_code', 'sku', 'title', 'body', 'brand', 'gender', 'type',
                'material', 'weight', 'categories', 'collections', 'attributes',
                'colourway_code', 'colourway_name', 'rgb', 'size', 'size_conversions',
                'price_single', 'price_pack', 'pack_qty', 'price_carton', 'carton_qty',
                'stock_fts', 'image_model', 'image_front', 'image_back', 'image_side', 'image_detail',
                'is_new', 'is_clearance',
            ]);

        if ($rows->isEmpty()) {
            return null;
        }

        $first = $rows->first();
        if (! $this->productMatchesCategory($first, $categoryName)) {
            return null;
        }

        $colours = [];
        foreach ($rows as $row) {
            $code = $row->colourway_code ?: 'default';

            if (! isset($colours[$code])) {
                // Prefer plain front shot for customizer; model shot is fallback.
                $frontRaw = $row->image_front ?: $row->image_model;
                $backRaw = $row->image_back ?: null;
                $frontUrl = $this->resolveOptionalImageUrl($frontRaw);
                $backUrl = $this->resolveOptionalImageUrl($backRaw) ?: $frontUrl;

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
                    // Plain catalogue images for the live customizer (per colourway)
                    'image_front' => $frontUrl,
                    'image_back' => $backUrl,
                    'has_plain_photo' => (bool) $frontUrl,
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

        foreach ($colours as &$colour) {
            $colour['sizes'] = $this->sortBySize($colour['sizes'], fn (array $s) => $s['size'] ?? '');
        }
        unset($colour);

        $colourList = array_values($colours);
        $minPrice = $rows->min('price_single');
        $attributes = $this->parseAttributes($first->attributes);
        $representativeId = (int) $rows->min('id');
        $uploadedCustomizer = $this->getUploadedCustomizerImagePath($representativeId);
        $fallbackCustomizer = asset('assets/images/customizer/tshirtcustom.png');
        // Prefer the first colourway's plain catalogue photo over the generic t-shirt
        $customizerImage = $colourList[0]['image_front']
            ?? $uploadedCustomizer
            ?? $fallbackCustomizer;

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
            'uploaded_customizer_image' => $uploadedCustomizer,
            'representative_id' => $representativeId,
        ];
    }

    public function getFilterOptions(string $categoryName): array
    {
        return $this->getFilterOptionsForCategory($categoryName);
    }

    public function getFilterOptionsFromProducts(Collection $products): array
    {
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

    public function getFilterOptionsForCategory(string $categoryName): array
    {
        $styleCodes = $this->getCategoryStyleCodes($categoryName);

        if ($styleCodes === []) {
            return [
                'brands' => [],
                'types' => [],
                'colours' => [],
                'price_min' => 0,
                'price_max' => 100,
            ];
        }

        $base = PcProduct::active()->whereIn('style_code', $styleCodes);
        $priceStats = (clone $base)->selectRaw('MIN(price_single) as min_p, MAX(price_single) as max_p')->first();
        $minRaw = (float) ($priceStats->min_p ?? 0);
        $maxRaw = (float) ($priceStats->max_p ?? 100);

        $brands = (clone $base)
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand')
            ->filter()
            ->values()
            ->all();

        $types = (clone $base)
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->filter()
            ->values()
            ->all();

        $colours = [];
        foreach (array_chunk($styleCodes, self::STYLE_CHUNK) as $chunk) {
            $colourRows = PcProduct::active()
                ->whereIn('style_code', $chunk)
                ->select(['colourway_name', 'colourway_code', 'rgb'])
                ->distinct()
                ->get();

            foreach ($colourRows as $row) {
                $name = $row->colourway_name ?: $row->colourway_code;
                if ($name && ! isset($colours[$name])) {
                    $colours[$name] = [
                        'name' => $name,
                        'hex' => $this->normalizeHex($row->rgb),
                    ];
                }
            }
        }

        $displayMin = ShopPricingService::displayUnitPrice($minRaw);
        $displayMax = ShopPricingService::displayUnitPrice($maxRaw);

        return [
            'brands' => $brands,
            'types' => $types,
            'colours' => array_values($colours),
            'price_min' => (int) floor($displayMin),
            'price_max' => max((int) ceil($displayMax), (int) floor($displayMin) + 1),
        ];
    }

    public function getRelatedProducts(string $categoryName, string $excludeStyleCode, int $limit = 4): Collection
    {
        $styleCodes = array_values(array_filter(
            $this->getCategoryStyleCodes($categoryName),
            fn (string $code) => $code !== $excludeStyleCode
        ));

        if ($styleCodes === []) {
            return collect();
        }

        $pick = array_slice($styleCodes, 0, $limit);

        return $this->buildSummariesForStyleCodes($pick, $categoryName);
    }

    public function getCustomizerImagePath(int $productId): string
    {
        return $this->getUploadedCustomizerImagePath($productId)
            ?? asset('assets/images/customizer/tshirtcustom.png');
    }

    public function getUploadedCustomizerImagePath(int $productId): ?string
    {
        $record = PcProductCustomizerImage::where('product_id', $productId)->first();

        if ($record && $record->image) {
            return $this->resolveImageUrl($record->image);
        }

        return null;
    }

    public function productsForSearch(): Collection
    {
        $styleCodes = PcProduct::active()
            ->distinct()
            ->orderBy('style_code')
            ->pluck('style_code')
            ->all();

        return $this->buildSummariesForStyleCodes($styleCodes);
    }

    /** @return list<string> */
    private function getCategoryStyleCodes(string $categoryName): array
    {
        $slug = $this->categorySlug($categoryName);
        $category = $this->buildCategoryIndex()[$slug] ?? null;

        if (! $category || empty($category['style_codes'])) {
            return [];
        }

        return array_keys($category['style_codes']);
    }

    private function resolveStyleCodeBySlug(string $categoryName, string $productSlug): ?string
    {
        foreach (array_chunk($this->getCategoryStyleCodes($categoryName), self::STYLE_CHUNK) as $chunk) {
            $rows = PcProduct::active()
                ->whereIn('style_code', $chunk)
                ->select(['style_code', 'title'])
                ->distinct()
                ->get();

            foreach ($rows as $row) {
                if ($this->productSlug($row->title, $row->style_code) === $productSlug) {
                    return $row->style_code;
                }
            }
        }

        return null;
    }

    private const CATEGORY_CACHE_KEY = 'pc_category_index_v3';

    private function buildCategoryIndex(): array
    {
        if ($this->categoryIndex !== null) {
            return $this->categoryIndex;
        }

        $cached = Cache::get(self::CATEGORY_CACHE_KEY);
        if (is_array($cached) && $cached !== []) {
            return $this->categoryIndex = $cached;
        }

        $index = $this->computeCategoryIndex();

        // Never cache an empty index — that hides categories for up to an hour
        // after a failed import / empty DB moment on live.
        if ($index !== []) {
            Cache::put(self::CATEGORY_CACHE_KEY, $index, now()->addHour());
        } else {
            Cache::forget(self::CATEGORY_CACHE_KEY);
        }

        return $this->categoryIndex = $index;
    }

    /**
     * @return array<string, array{name:string,slug:string,count:int,style_codes:array<string,bool>}>
     */
    private function computeCategoryIndex(): array
    {
        $index = [];

        // One row per product style — avoids counting every colour/size SKU.
        foreach (
            PcProduct::active()
                ->whereNotNull('categories')
                ->whereNotNull('style_code')
                ->where('style_code', '!=', '')
                ->selectRaw('style_code, MIN(categories) as categories')
                ->groupBy('style_code')
                ->cursor() as $row
        ) {
            $styleCode = strtoupper(trim((string) $row->style_code));
            if ($styleCode === '') {
                continue;
            }

            foreach ($this->parseCategoryNames($row->categories) as $name) {
                $slug = $this->categorySlug($name);
                if (! isset($index[$slug])) {
                    $index[$slug] = [
                        'name' => $name,
                        'slug' => $slug,
                        'count' => 0,
                        'style_codes' => [],
                    ];
                }

                if (! isset($index[$slug]['style_codes'][$styleCode])) {
                    $index[$slug]['style_codes'][$styleCode] = true;
                }
            }
        }

        foreach ($index as &$category) {
            $category['count'] = count($category['style_codes']);
        }
        unset($category);

        uasort($index, fn ($a, $b) => strcasecmp($a['name'], $b['name']));

        return $index;
    }

    public function clearCategoryCache(): void
    {
        Cache::forget(self::CATEGORY_CACHE_KEY);
        Cache::forget('pc_category_index_v2');
        $this->categoryIndex = null;
    }

    /**
     * Build one card summary per style code without loading every SKU row.
     *
     * @param  list<string>  $styleCodes
     */
    private function buildSummariesForStyleCodes(array $styleCodes, ?string $categoryName = null): Collection
    {
        if ($styleCodes === []) {
            return collect();
        }

        $groups = [];

        foreach (array_chunk($styleCodes, self::STYLE_CHUNK) as $chunk) {
            $representativeIds = PcProduct::active()
                ->whereIn('style_code', $chunk)
                ->selectRaw('MIN(id) as id')
                ->groupBy('style_code')
                ->pluck('id');

            if ($representativeIds->isEmpty()) {
                continue;
            }

            $rows = PcProduct::active()
                ->whereIn('id', $representativeIds)
                ->get([
                    'id', 'style_code', 'title', 'brand', 'type', 'categories',
                    'price_single', 'image_front', 'image_model', 'is_new', 'is_clearance',
                ]);

            $priceStats = PcProduct::active()
                ->whereIn('style_code', $chunk)
                ->selectRaw('style_code, MIN(price_single) as min_price, MAX(price_single) as max_price, MAX(is_new) as is_new, MAX(is_clearance) as is_clearance')
                ->groupBy('style_code')
                ->get()
                ->keyBy('style_code');

            $colourRows = PcProduct::active()
                ->whereIn('style_code', $chunk)
                ->select(['style_code', 'colourway_name', 'colourway_code', 'rgb'])
                ->distinct()
                ->get()
                ->groupBy('style_code');

            foreach ($rows as $row) {
                if ($categoryName && ! $this->productMatchesCategory($row, $categoryName)) {
                    continue;
                }

                $stats = $priceStats->get($row->style_code);
                $styleCode = $row->style_code;
                $colours = [];
                $colourNames = [];

                foreach ($colourRows->get($styleCode, collect()) as $colourRow) {
                    $colourName = $colourRow->colourway_name ?: $colourRow->colourway_code;
                    if (! $colourName || in_array($colourName, $colourNames, true)) {
                        continue;
                    }

                    $colourNames[] = $colourName;
                    $colours[] = [
                        'name' => $colourName,
                        'hex' => $this->normalizeHex($colourRow->rgb),
                    ];
                }

                $groups[$styleCode] = [
                    'style_code' => $styleCode,
                    'title' => $row->title,
                    'slug' => $this->productSlug($row->title, $styleCode),
                    'brand' => $row->brand,
                    'type' => $row->type,
                    'category_name' => $categoryName ?? ($this->parseCategoryNames($row->categories)[0] ?? ''),
                    'category_slug' => $categoryName
                        ? $this->categorySlug($categoryName)
                        : $this->categorySlug($this->parseCategoryNames($row->categories)[0] ?? 'shop'),
                    'min_price' => (float) ($stats->min_price ?? $row->price_single),
                    'max_price' => (float) ($stats->max_price ?? $row->price_single),
                    'image' => $this->resolveImageUrl($row->image_front ?: $row->image_model),
                    'is_new' => (bool) ($stats->is_new ?? $row->is_new),
                    'is_clearance' => (bool) ($stats->is_clearance ?? $row->is_clearance),
                    'colours' => $colours,
                    'url' => null,
                ];
            }
        }

        return collect($groups)
            ->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
            ->map(function (array $product) {
                $product['min_price'] = ShopPricingService::displayUnitPrice($product['min_price']);
                $product['max_price'] = ShopPricingService::displayUnitPrice($product['max_price']);
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
        if (! $attributes) {
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
        if (! $value) {
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

        if (! str_starts_with($rgb, '#')) {
            $rgb = '#' . $rgb;
        }

        return strtolower($rgb);
    }

    /**
     * Sort garment sizes small → large (XS…5XL, then numeric waist/chest, then alpha).
     *
     * @template T
     * @param  list<T>  $items
     * @param  callable(T): ?string  $getSize
     * @return list<T>
     */
    public function sortBySize(array $items, callable $getSize): array
    {
        usort($items, function ($a, $b) use ($getSize) {
            $ka = $this->sizeSortKey($getSize($a));
            $kb = $this->sizeSortKey($getSize($b));

            return $ka <=> $kb;
        });

        return array_values($items);
    }

    /**
     * @return array{0:int,1:float,2:string}
     */
    public function sizeSortKey(?string $size): array
    {
        $raw = trim((string) $size);
        $normalized = strtoupper(preg_replace('/[\s_\-]+/', '', $raw) ?? '');

        static $named = [
            'ONESIZE' => 5.0,
            'OSFA' => 5.0,
            'OS' => 5.0,
            'ONE' => 5.0,
            'XXXS' => 10.0,
            '3XS' => 10.0,
            'XXS' => 20.0,
            '2XS' => 20.0,
            'XS' => 30.0,
            'S' => 40.0,
            'SM' => 40.0,
            'SMALL' => 40.0,
            'M' => 50.0,
            'MD' => 50.0,
            'MED' => 50.0,
            'MEDIUM' => 50.0,
            'L' => 60.0,
            'LG' => 60.0,
            'LARGE' => 60.0,
            'XL' => 70.0,
            'XLG' => 70.0,
            'XXL' => 80.0,
            '2XL' => 80.0,
            '2X' => 80.0,
            'XXXL' => 90.0,
            '3XL' => 90.0,
            '3X' => 90.0,
            'XXXXL' => 100.0,
            '4XL' => 100.0,
            '4X' => 100.0,
            '5XL' => 110.0,
            '5X' => 110.0,
            '6XL' => 120.0,
            '6X' => 120.0,
            '7XL' => 130.0,
            '7X' => 130.0,
            '8XL' => 140.0,
            '8X' => 140.0,
        ];

        if ($normalized !== '' && isset($named[$normalized])) {
            return [0, $named[$normalized], $raw];
        }

        // e.g. 9XL, 10XL
        if (preg_match('/^(\d+)X(?:L|LG|LGE)?$/', $normalized, $m)) {
            return [0, 60.0 + ((float) $m[1] * 10.0), $raw];
        }

        // Pure numeric (waist / chest / EU)
        if (preg_match('/^(\d+(?:\.\d+)?)$/', $normalized, $m)) {
            return [1, (float) $m[1], $raw];
        }

        // Leading number groups: 3-4, 28/30, 9/11
        if (preg_match('/^(\d+(?:\.\d+)?)/', $normalized, $m)) {
            return [1, (float) $m[1], $raw];
        }

        return [2, 0.0, strtolower($raw)];
    }

    private function resolveImageUrl(?string $path): string
    {
        return $this->resolveOptionalImageUrl($path) ?? asset('assets/images/prod1.jpg');
    }

    public function imageUrl(?string $path): ?string
    {
        return $this->resolveOptionalImageUrl($path);
    }

    private function resolveOptionalImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(ltrim($path, '/'));
    }
}
