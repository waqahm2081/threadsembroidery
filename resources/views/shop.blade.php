@extends('layouts.app')

@section('title', 'Shop — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}">
@endpush

@section('content')

<!-- ============================================================
         SHOP PAGE HEADER
    ============================================================ -->
    <section id="shop-hero">
        <div class="sh-container">
            <div class="sh-eyebrow">
                <span class="sh-eyebrow-dot"></span>
                Custom Branded Garments
            </div>
            <h1 class="sh-title">Our <span class="sh-accent">Shop</span></h1>
            <p class="sh-sub">Browse our full range of customisable workwear, sportswear &amp; uniforms.</p>
            <nav class="sh-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="sh-sep"><i class="bi bi-chevron-right"></i></span>
                <span>Shop</span>
            </nav>
        </div>
    </section>


    <!-- ============================================================
         SHOP BODY — Sidebar filters + Product grid
    ============================================================ -->
    <section id="shop-body">
        <div class="sb-container">

            <!-- ── Mobile Filter Toggle ── -->
            <button class="sb-mobile-filter-btn" id="sbMobileFilterBtn">
                <i class="bi bi-sliders"></i> Filters
                <span class="sb-filter-count" id="sbFilterCount" style="display:none">0</span>
            </button>

            <!-- ── SIDEBAR ── -->
            <aside class="sb-sidebar" id="sbSidebar">
                <div class="sb-sidebar-inner">

                    <!-- Header (mobile) -->
                    <div class="sb-sidebar-header">
                        <span class="sb-sidebar-title">Filters</span>
                        <button class="sb-sidebar-close" id="sbSidebarClose" aria-label="Close filters">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <!-- Clear All -->
                    <button class="sb-clear-all" id="sbClearAll">Clear all filters <i class="bi bi-x-circle"></i></button>

                    <!-- ── Price Range ── -->
                    <div class="sb-filter-group" id="sfgPrice">
                        <button class="sb-filter-toggle" aria-expanded="true">
                            Price Range <i class="bi bi-chevron-down sb-chevron"></i>
                        </button>
                        <div class="sb-filter-body">
                            <div class="sb-price-range">
                                <div class="sb-price-labels">
                                    <span id="priceMin">£0</span>
                                    <span id="priceMax">£100+</span>
                                </div>
                                <div class="sb-range-track" id="priceTrack">
                                    <div class="sb-range-fill" id="priceFill"></div>
                                    <input type="range" min="0" max="100" value="0"  id="priceRangeMin" class="sb-range-input sb-range-input--min" aria-label="Min price">
                                    <input type="range" min="0" max="100" value="100" id="priceRangeMax" class="sb-range-input sb-range-input--max" aria-label="Max price">
                                </div>
                                <div class="sb-price-presets">
                                    <button class="sb-preset-btn active" data-min="0" data-max="100">All</button>
                                    <button class="sb-preset-btn" data-min="0" data-max="10">Under £10</button>
                                    <button class="sb-preset-btn" data-min="10" data-max="25">£10–£25</button>
                                    <button class="sb-preset-btn" data-min="25" data-max="50">£25–£50</button>
                                    <button class="sb-preset-btn" data-min="50" data-max="100">£50+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── Product Type ── -->
                    <div class="sb-filter-group" id="sfgType">
                        <button class="sb-filter-toggle" aria-expanded="true">
                            Product Type <i class="bi bi-chevron-down sb-chevron"></i>
                        </button>
                        <div class="sb-filter-body">
                            <div class="sb-checkbox-list">
                                <label class="sb-checkbox-item"><input type="checkbox" value="Accessories"><span class="sb-checkbox-label">Accessories</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="All in Ones & Pyjamas"><span class="sb-checkbox-label">All in Ones &amp; Pyjamas</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Aprons"><span class="sb-checkbox-label">Aprons</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Bags"><span class="sb-checkbox-label">Bags</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Blankets"><span class="sb-checkbox-label">Blankets</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Children"><span class="sb-checkbox-label">Children</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Coveralls"><span class="sb-checkbox-label">Coveralls</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Dresses & Skirts"><span class="sb-checkbox-label">Dresses &amp; Skirts</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Fleece"><span class="sb-checkbox-label">Fleece</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Footwear"><span class="sb-checkbox-label">Footwear</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Gilets & Bodywarmers"><span class="sb-checkbox-label">Gilets &amp; Bodywarmers</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Gloves"><span class="sb-checkbox-label">Gloves</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Headwear"><span class="sb-checkbox-label">Headwear</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Hi-Vis"><span class="sb-checkbox-label">Hi-Vis</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Hoods"><span class="sb-checkbox-label">Hoods</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Jackets"><span class="sb-checkbox-label">Jackets</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Knitwear"><span class="sb-checkbox-label">Knitwear</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Leggings"><span class="sb-checkbox-label">Leggings</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Leisure Rugby Shirts"><span class="sb-checkbox-label">Leisure Rugby Shirts</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Organic & Recycled"><span class="sb-checkbox-label">Organic &amp; Recycled</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Polos"><span class="sb-checkbox-label">Polos</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Scarves"><span class="sb-checkbox-label">Scarves</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Shirts & Blouses"><span class="sb-checkbox-label">Shirts &amp; Blouses</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Shorts"><span class="sb-checkbox-label">Shorts</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Socks"><span class="sb-checkbox-label">Socks</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Soft Shell"><span class="sb-checkbox-label">Soft Shell</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Sweatshirts"><span class="sb-checkbox-label">Sweatshirts</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="T-Shirts"><span class="sb-checkbox-label">T-Shirts</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Trousers"><span class="sb-checkbox-label">Trousers</span></label>
                                <label class="sb-checkbox-item"><input type="checkbox" value="Tunics"><span class="sb-checkbox-label">Tunics</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- ── Brands ── -->
                    <div class="sb-filter-group" id="sfgBrands">
                        <button class="sb-filter-toggle" aria-expanded="true">
                            Brands <i class="bi bi-chevron-down sb-chevron"></i>
                        </button>
                        <div class="sb-filter-body">
                            <div class="sb-search-mini">
                                <i class="bi bi-search"></i>
                                <input type="text" id="brandSearch" placeholder="Search brands…" />
                            </div>
                            <div class="sb-checkbox-list sb-brands-list" id="brandsList">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- ── Colours ── -->
                    <div class="sb-filter-group" id="sfgColours">
                        <button class="sb-filter-toggle" aria-expanded="true">
                            Colours <i class="bi bi-chevron-down sb-chevron"></i>
                        </button>
                        <div class="sb-filter-body">
                            <div class="sb-colour-swatches" id="colourSwatches">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>

                </div>
            </aside>
            <!-- Sidebar backdrop (mobile) -->
            <div class="sb-sidebar-backdrop" id="sbBackdrop"></div>

            <!-- ── MAIN CONTENT ── -->
            <div class="sb-main">

                <!-- Toolbar -->
                <div class="sb-toolbar">
                    <div class="sb-toolbar-left">
                        <span class="sb-result-count" id="sbResultCount">Showing <strong>24</strong> products</span>
                    </div>
                    <div class="sb-toolbar-right">
                        <label class="sb-sort-label">Sort by:</label>
                        <select class="sb-sort-select" id="sbSortSelect">
                            <option value="featured">Featured</option>
                            <option value="az">Name: A–Z</option>
                            <option value="za">Name: Z–A</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="rating">Top Rated</option>
                        </select>
                        <div class="sb-view-btns">
                            <button class="sb-view-btn active" id="viewGrid" aria-label="Grid view"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                            <button class="sb-view-btn" id="viewList" aria-label="List view"><i class="bi bi-list-ul"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Active filter pills -->
                <div class="sb-active-filters" id="sbActiveFilters"></div>

                <!-- Products Grid -->
                <div class="sb-products-grid" id="sbProductsGrid">

                    <!-- Product cards are rendered by JS, but we pre-load 24 static cards -->

                </div>

                <!-- Pagination -->
                <div class="sb-pagination">
                    <button class="sb-page-btn sb-page-btn--prev disabled"><i class="bi bi-chevron-left"></i> Prev</button>
                    <div class="sb-page-numbers">
                        <button class="sb-page-num active">1</button>
                        <button class="sb-page-num">2</button>
                        <button class="sb-page-num">3</button>
                        <span class="sb-page-ellipsis">…</span>
                        <button class="sb-page-num">8</button>
                    </div>
                    <button class="sb-page-btn sb-page-btn--next">Next <i class="bi bi-chevron-right"></i></button>
                </div>

            </div>
        </div>
    </section>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/shop.js') }}"></script>
@endpush
