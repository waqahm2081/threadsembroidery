@extends('layouts.app')

@section('title', $category['name'] . ' — Shop — Threads Embroidery & Print Ltd')

@section('body_class')
shop-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.1">
<link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}">
@endpush

@section('content')

<section id="shop-hero">
    <div class="sh-container">
        <div class="sh-eyebrow">
            <span class="sh-eyebrow-dot"></span>
            Custom Branded Garments
        </div>
        <h1 class="sh-title">{{ $category['name'] }} <span class="sh-accent">Shop</span></h1>
        <p class="sh-sub">Browse customisable {{ strtolower($category['name']) }} from our catalogue.</p>
        <nav class="sh-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="sh-sep"><i class="bi bi-chevron-right"></i></span>
            <a href="{{ route('shop') }}">Shop</a>
            <span class="sh-sep"><i class="bi bi-chevron-right"></i></span>
            <span>{{ $category['name'] }}</span>
        </nav>
    </div>
</section>

<section id="shop-body">
    <div class="sb-container">
        <button class="sb-mobile-filter-btn" id="sbMobileFilterBtn">
            <i class="bi bi-sliders"></i> Filters
            <span class="sb-filter-count" id="sbFilterCount" style="display:none">0</span>
        </button>

        <aside class="sb-sidebar" id="sbSidebar">
            <div class="sb-sidebar-inner">
                <div class="sb-sidebar-header">
                    <span class="sb-sidebar-title">Filters</span>
                    <button class="sb-sidebar-close" id="sbSidebarClose" aria-label="Close filters">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <button class="sb-clear-all" id="sbClearAll">Clear all filters <i class="bi bi-x-circle"></i></button>

                <div class="sb-filter-group" id="sfgPrice">
                    <button class="sb-filter-toggle" aria-expanded="true">
                        Price Range <i class="bi bi-chevron-down sb-chevron"></i>
                    </button>
                    <div class="sb-filter-body">
                        <div class="sb-price-range">
                            <div class="sb-price-labels">
                                <span id="priceMin">£{{ $filters['price_min'] }}</span>
                                <span id="priceMax">£{{ $filters['price_max'] }}+</span>
                            </div>
                            <div class="sb-range-track" id="priceTrack">
                                <div class="sb-range-fill" id="priceFill"></div>
                                <input type="range" min="{{ $filters['price_min'] }}" max="{{ $filters['price_max'] }}" value="{{ $filters['price_min'] }}" id="priceRangeMin" class="sb-range-input sb-range-input--min" aria-label="Min price">
                                <input type="range" min="{{ $filters['price_min'] }}" max="{{ $filters['price_max'] }}" value="{{ $filters['price_max'] }}" id="priceRangeMax" class="sb-range-input sb-range-input--max" aria-label="Max price">
                            </div>
                        </div>
                    </div>
                </div>

                @if(!empty($filters['types']))
                <div class="sb-filter-group" id="sfgType">
                    <button class="sb-filter-toggle" aria-expanded="true">
                        Product Type <i class="bi bi-chevron-down sb-chevron"></i>
                    </button>
                    <div class="sb-filter-body">
                        <div class="sb-checkbox-list">
                            @foreach($filters['types'] as $type)
                                <label class="sb-checkbox-item">
                                    <input type="checkbox" value="{{ $type }}">
                                    <span class="sb-checkbox-label">{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($filters['brands']))
                <div class="sb-filter-group" id="sfgBrands">
                    <button class="sb-filter-toggle" aria-expanded="true">
                        Brands <i class="bi bi-chevron-down sb-chevron"></i>
                    </button>
                    <div class="sb-filter-body">
                        <div class="sb-search-mini">
                            <i class="bi bi-search"></i>
                            <input type="text" id="brandSearch" placeholder="Search brands…" />
                        </div>
                        <div class="sb-checkbox-list sb-brands-list" id="brandsList"></div>
                    </div>
                </div>
                @endif

                @if(!empty($filters['colours']))
                <div class="sb-filter-group" id="sfgColours">
                    <button class="sb-filter-toggle" aria-expanded="true">
                        Colours <i class="bi bi-chevron-down sb-chevron"></i>
                    </button>
                    <div class="sb-filter-body">
                        <div class="sb-colour-swatches" id="colourSwatches"></div>
                    </div>
                </div>
                @endif
            </div>
        </aside>

        <div class="sb-sidebar-backdrop" id="sbBackdrop"></div>

        <div class="sb-main">
            <div class="sb-toolbar">
                <div class="sb-toolbar-left">
                    <span class="sb-result-count" id="sbResultCount">Showing <strong>{{ $products->count() }}</strong> products</span>
                </div>
                <div class="sb-toolbar-right">
                    <label class="sb-sort-label">Sort by:</label>
                    <select class="sb-sort-select" id="sbSortSelect">
                        <option value="featured">Featured</option>
                        <option value="az">Name: A–Z</option>
                        <option value="za">Name: Z–A</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                    </select>
                    <div class="sb-view-btns">
                        <button class="sb-view-btn active" id="viewGrid" aria-label="Grid view"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                        <button class="sb-view-btn" id="viewList" aria-label="List view"><i class="bi bi-list-ul"></i></button>
                    </div>
                </div>
            </div>

            <div class="sb-active-filters" id="sbActiveFilters"></div>
            <div class="sb-products-grid" id="sbProductsGrid"></div>
            <div class="sb-load-sentinel" id="sbLoadSentinel" aria-hidden="true">
                <span class="sb-load-spinner"></span>
            </div>
            <div class="sb-empty-state" id="sbEmptyState" style="display:none;">
                <i class="bi bi-search"></i>
                <p>No products match your filters.</p>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
@php
    $shopData = [
        'category' => $category,
        'products' => $products->values(),
        'filters' => $filters,
    ];
@endphp
window.__SHOP_DATA__ = @json($shopData, JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP);
</script>
<script src="{{ asset('assets/js/shop.js') }}?v=1.3"></script>
@endpush