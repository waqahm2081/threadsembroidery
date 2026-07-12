@extends('layouts.app')

@section('title', 'Classic Embroidered Polo Shirt — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/product-detail.css') }}">
@endpush

@section('content')

<!-- ============================================================
         1. BREADCRUMB
    ============================================================ -->
    <section id="pd-breadcrumb">
    <div class="pd-breadcrumb-inner">
        <nav aria-label="Breadcrumb">
        <ol class="pd-breadcrumb-list">
            <li><a href="{{ route('home') }}"><i class="bi bi-house"></i> Home</a></li>
            <li class="pd-breadcrumb-sep"><i class="bi bi-chevron-right"></i></li>
            <li><a href="#">Catalogue</a></li>
            <li class="pd-breadcrumb-sep"><i class="bi bi-chevron-right"></i></li>
            <li><a href="#">Corporatewear</a></li>
            <li class="pd-breadcrumb-sep"><i class="bi bi-chevron-right"></i></li>
            <li aria-current="page">Classic Embroidered Polo Shirt</li>
        </ol>
        </nav>
        <a href="javascript:history.back()" class="pd-breadcrumb-back">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
    </section>


    <!-- ============================================================
         2. PRODUCT DETAIL — full width, 2 columns
    ============================================================ -->
    <section id="pd-detail">
        <div class="pd-detail-grid">

            <!-- ── LEFT: Images ── -->
            <div class="pd-images-col">
                <div class="pd-main-img-wrap">
                    <img src="{{ asset('assets/images/customize.jpeg') }}" alt="Classic Embroidered Polo Shirt" id="pdMainImage" class="pd-main-img">
                    <div class="pd-zoom-hint"><i class="bi bi-zoom-in"></i></div>
                    <span class="pd-img-badge">Best Seller</span>
                </div>
                <div class="pd-thumbs">
                    <button class="pd-thumb active" data-src="{{ asset('assets/images/customize.jpeg') }}" aria-label="View image 1">
                        <img src="{{ asset('assets/images/customize.jpeg') }}" alt="Polo shirt front">
                    </button>
                    <button class="pd-thumb active" data-src="{{ asset('assets/images/customizegal1.jpeg') }}" aria-label="View image 1">
                        <img src="{{ asset('assets/images/customizegal1.jpeg') }}" alt="Polo shirt front">
                    </button>
                    <button class="pd-thumb" data-src="{{ asset('assets/images/customizegal2.jpeg') }}" aria-label="View image 2">
                        <img src="{{ asset('assets/images/customizegal2.jpeg') }}" alt="Polo shirt back">
                    </button>
                    <button class="pd-thumb" data-src="{{ asset('assets/images/customizegal3.jpeg') }}" aria-label="View image 3">
                        <img src="{{ asset('assets/images/customizegal3.jpeg') }}" alt="Polo shirt detail">
                    </button>
                    <button class="pd-thumb" data-src="{{ asset('assets/images/customizegal4.jpeg') }}" aria-label="View image 4">
                        <img src="{{ asset('assets/images/customizegal4.jpeg') }}" alt="Polo shirt worn">
                    </button>
                </div>
            </div>

            <!-- ── RIGHT: Product Info ── -->
            <div class="pd-info-col">
                <div class="pd-info-top">
                    <span class="pd-category-tag"><i class="bi bi-tag"></i> Corporatewear</span>
                    <span class="pd-rating">
                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        <span class="pd-rating-val">4.9</span>
                        <span class="pd-rating-count">(127 reviews)</span>
                    </span>
                </div>

                <h1 class="pd-product-name">Classic Embroidered Polo Shirt</h1>

                <div class="pd-price-row">
                    <span class="pd-price-from">From</span>
                    <span class="pd-price">£8<small>.50</small></span>
                    <span class="pd-price-unit">per unit</span>
                    <span class="pd-price-note"><i class="bi bi-info-circle"></i> Price varies by quantity</span>
                </div>

                <p class="pd-short-desc">
                    A wardrobe staple for any professional team. Our Classic Polo Shirt is crafted from premium piqué cotton and features your logo embroidered with precision to the left chest, sleeve, or back — wherever you need it most.
                </p>

                <div class="pd-divider"></div>

                <div class="pd-option-group">
                    <div class="pd-option-label-row">
                        <span class="pd-option-label">Colour</span>
                        <span class="pd-selected-colour-name" id="pdColourName">Navy</span>
                    </div>
                    <div class="pd-swatches" id="pdSwatches">
                        <button class="pd-swatch active" data-colour="Navy"         style="background:#1a1a2e;" aria-label="Navy" title="Navy"></button>
                        <button class="pd-swatch"        data-colour="Black"        style="background:#111111;" aria-label="Black" title="Black"></button>
                        <button class="pd-swatch"        data-colour="White"        style="background:#f0f0f0; border-color:rgba(0,0,0,0.15);" aria-label="White" title="White"></button>
                        <button class="pd-swatch"        data-colour="Red"          style="background:#c0392b;" aria-label="Red" title="Red"></button>
                        <button class="pd-swatch"        data-colour="Royal Blue"   style="background:#2471a3;" aria-label="Royal Blue" title="Royal Blue"></button>
                        <button class="pd-swatch"        data-colour="Bottle Green" style="background:#1e6641;" aria-label="Bottle Green" title="Bottle Green"></button>
                        <button class="pd-swatch"        data-colour="Orange"       style="background:#ee6023;" aria-label="Orange" title="Orange"></button>
                        <button class="pd-swatch"        data-colour="Heather Grey" style="background:#9ea5aa;" aria-label="Heather Grey" title="Heather Grey"></button>
                    </div>
                </div>

                <div class="pd-divider"></div>

                <!-- ── Decoration Technology ── -->
                <div class="pd-option-group">
                    <div class="pd-option-label-row">
                        <span class="pd-option-label">Decoration Technology</span>
                        <span class="pd-selected-option-name" id="pdTechName">Direct to Garment</span>
                    </div>
                    <div class="pd-option-tiles" id="pdTechTiles">
                        <button class="pd-option-tile active" data-option-group="tech" data-value="Direct to Garment">
                            <i class="bi bi-printer"></i>
                            <span class="pd-tile-label">Direct to Garment</span>
                            <span class="pd-tile-sub">Photorealistic detail</span>
                        </button>
                        <button class="pd-option-tile" data-option-group="tech" data-value="Screen Printing">
                            <i class="bi bi-layers"></i>
                            <span class="pd-tile-label">Screen Printing</span>
                            <span class="pd-tile-sub">Bold, vibrant colour</span>
                        </button>
                    </div>
                </div>

                <div class="pd-divider"></div>

                <!-- ── Decoration Area ── -->
                <div class="pd-option-group">
                    <div class="pd-option-label-row">
                        <span class="pd-option-label">Decoration Area</span>
                        <span class="pd-selected-option-name" id="pdAreaName">Full Front</span>
                    </div>
                    <div class="pd-option-tiles" id="pdAreaTiles">
                        <button class="pd-option-tile active" data-option-group="area" data-value="Full Front">
                            <span class="pd-tile-label">Full Front</span>
                            <span class="pd-tile-sub">Max impact placement</span>
                        </button>
                        <button class="pd-option-tile" data-option-group="area" data-value="Left Chest">
                            <span class="pd-tile-label">Left Chest</span>
                            <span class="pd-tile-sub">Classic logo position</span>
                        </button>
                    </div>
                </div>

                <div class="pd-divider"></div>

                <div class="pd-option-group">
                    <div class="pd-option-label-row">
                        <span class="pd-option-label">Sizes &amp; Quantities</span>
                        <a href="#" class="pd-size-guide-link"><i class="bi bi-rulers"></i> Size Guide</a>
                    </div>
                    <div class="pd-size-qty-grid">
                        <div class="pd-size-qty-header"><span>Size</span><span>Quantity</span></div>
                        <div class="pd-size-row-item">
                            <label class="pd-size-label" for="qty-xs"><span class="pd-size-chip">XS</span></label>
                            <div class="pd-qty-ctrl">
                                <button class="pd-qty-btn pd-qty-minus" aria-label="Decrease XS">−</button>
                                <input type="number" id="qty-xs" class="pd-qty-input" value="0" min="0" max="999">
                                <button class="pd-qty-btn pd-qty-plus" aria-label="Increase XS">+</button>
                            </div>
                        </div>
                        <div class="pd-size-row-item">
                            <label class="pd-size-label" for="qty-s"><span class="pd-size-chip">S</span></label>
                            <div class="pd-qty-ctrl">
                                <button class="pd-qty-btn pd-qty-minus" aria-label="Decrease S">−</button>
                                <input type="number" id="qty-s" class="pd-qty-input" value="0" min="0" max="999">
                                <button class="pd-qty-btn pd-qty-plus" aria-label="Increase S">+</button>
                            </div>
                        </div>
                        <div class="pd-size-row-item">
                            <label class="pd-size-label" for="qty-m"><span class="pd-size-chip">M</span></label>
                            <div class="pd-qty-ctrl">
                                <button class="pd-qty-btn pd-qty-minus" aria-label="Decrease M">−</button>
                                <input type="number" id="qty-m" class="pd-qty-input" value="5" min="0" max="999">
                                <button class="pd-qty-btn pd-qty-plus" aria-label="Increase M">+</button>
                            </div>
                        </div>
                        <div class="pd-size-row-item">
                            <label class="pd-size-label" for="qty-l"><span class="pd-size-chip">L</span></label>
                            <div class="pd-qty-ctrl">
                                <button class="pd-qty-btn pd-qty-minus" aria-label="Decrease L">−</button>
                                <input type="number" id="qty-l" class="pd-qty-input" value="5" min="0" max="999">
                                <button class="pd-qty-btn pd-qty-plus" aria-label="Increase L">+</button>
                            </div>
                        </div>
                        <div class="pd-size-row-item">
                            <label class="pd-size-label" for="qty-xl"><span class="pd-size-chip">XL</span></label>
                            <div class="pd-qty-ctrl">
                                <button class="pd-qty-btn pd-qty-minus" aria-label="Decrease XL">−</button>
                                <input type="number" id="qty-xl" class="pd-qty-input" value="0" min="0" max="999">
                                <button class="pd-qty-btn pd-qty-plus" aria-label="Increase XL">+</button>
                            </div>
                        </div>
                        <div class="pd-size-row-item">
                            <label class="pd-size-label" for="qty-2xl"><span class="pd-size-chip">2XL</span></label>
                            <div class="pd-qty-ctrl">
                                <button class="pd-qty-btn pd-qty-minus" aria-label="Decrease 2XL">−</button>
                                <input type="number" id="qty-2xl" class="pd-qty-input" value="0" min="0" max="999">
                                <button class="pd-qty-btn pd-qty-plus" aria-label="Increase 2XL">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="pd-qty-summary">
                        <div class="pd-qty-summary-item">
                            <span>Total Units</span>
                            <strong id="pdTotalUnits">10</strong>
                        </div>
                        <div class="pd-qty-summary-divider"></div>
                        <div class="pd-qty-summary-item">
                            <span>Estimated Total</span>
                            <strong id="pdEstTotal" class="pd-est-total">£85.00</strong>
                        </div>
                    </div>
                </div>

                <div class="pd-divider"></div>

                <a href="#" class="pd-customize-btn">
                    <span class="pd-customize-btn-inner">
                        <i class="bi bi-magic"></i>
                        Upload Your Design / Customize
                    </span>
                    <span class="pd-customize-btn-glow"></span>
                </a>

                <div class="pd-trust-row">
                    <span class="pd-trust-item"><i class="bi bi-truck"></i> Free UK Delivery</span>
                    <span class="pd-trust-item"><i class="bi bi-pencil-square"></i> Free Artwork</span>
                    <span class="pd-trust-item"><i class="bi bi-calendar-check"></i> 10–14 Day Turnaround</span>
                </div>
            </div>

        </div>
    </section>


    <!-- ============================================================
         3. DESCRIPTION + FEATURES
    ============================================================ -->
    <section id="pd-desc">
        <div class="pd-desc-container">
            <div class="pd-tabs">
                <button class="pd-tab active" data-tab="description">Description</button>
                <button class="pd-tab" data-tab="features">Product Features</button>
            </div>
            <div class="pd-tab-panel active" id="pd-panel-description">
                <div class="pd-desc-content">
                    <h3 class="pd-desc-heading">About This Product</h3>
                    <p>The Classic Embroidered Polo Shirt is the gold standard in professional branded workwear. Constructed from a 220gsm piqué cotton blend, it balances breathability and durability — making it suitable for everything from front-of-house hospitality roles to corporate events and outdoor fieldwork.</p>
                    <p>Each shirt is precision-embroidered in our UK facility using industry-leading Tajima machinery. Your logo is digitised by our in-house artwork team at no extra cost, ensuring the finest stitch quality and colour accuracy across every garment in your order — whether you order 10 or 1,000.</p>
                    <p>Available in a wide range of colourways and sizes from XS to 3XL, this polo offers true versatility for any team. The fabric is easy-care and machine washable, retaining its shape and embroidery quality wash after wash. It's not just a polo shirt — it's a walking billboard for your brand.</p>
                </div>
            </div>
            <div class="pd-tab-panel" id="pd-panel-features">
                <div class="pd-features-grid">
                    <div class="pd-features-col">
                        <h3 class="pd-desc-heading">Product Specifications</h3>
                        <table class="pd-spec-table">
                            <tbody>
                                <tr><td>Fabric</td><td>220gsm Piqué Cotton Blend</td></tr>
                                <tr><td>Fit</td><td>Regular fit</td></tr>
                                <tr><td>Collar</td><td>Ribbed polo collar with two-button placket</td></tr>
                                <tr><td>Sizes</td><td>XS — 3XL</td></tr>
                                <tr><td>Care</td><td>Machine washable at 40°C</td></tr>
                                <tr><td>Certification</td><td>Oeko-Tex Standard 100</td></tr>
                                <tr><td>Origin</td><td>Embroidered in the UK</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="pd-features-col">
                        <h3 class="pd-desc-heading">Key Features</h3>
                        <ul class="pd-feature-list">
                            <li>Premium 220gsm piqué cotton for a professional finish</li>
                            <li>Free logo digitisation by our in-house artwork team</li>
                            <li>Consistent colour-matching across bulk orders</li>
                            <li>Embroidery positions: left chest, right chest, sleeve, back</li>
                            <li>Suitable for corporate, hospitality, sports, and schoolwear</li>
                            <li>Shrink-resistant and shape-retaining after repeated washing</li>
                            <li>Available in 12+ colourways to match your brand palette</li>
                            <li>Completed within 10–14 working days of artwork approval</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ============================================================
         4. RELATED PRODUCTS
    ============================================================ -->
    <section id="pd-related">
        <div class="pd-related-container">
            <div class="pd-related-header">
                <div class="pd-eyebrow">
                    <span class="pd-eyebrow-dot"></span>
                    You May Also Like
                </div>
                <h2 class="pd-related-heading">Related <span class="pd-related-accent">Products</span></h2>
            </div>
            <div class="pd-related-grid">
                <article class="pd-related-card">
                    <div class="pd-related-img-wrap">
                        <img src="{{ asset('assets/images/prod1.jpg') }}" alt="Premium Softshell Jacket" loading="lazy">
                        <div class="pd-related-actions">
                            <a href="#" class="pd-related-quick-btn"><i class="bi bi-magic"></i> Customise</a>
                        </div>
                        <span class="pd-related-badge pd-related-badge--pop">Popular</span>
                    </div>
                    <div class="pd-related-body">
                        <span class="pd-related-cat"><i class="bi bi-tag"></i> Workwear</span>
                        <h3 class="pd-related-name">Premium Softshell Jacket</h3>
                        <div class="pd-related-footer">
                            <span class="pd-related-price">From £22<small>.00</small></span>
                            <a href="{{ route('product-detail') }}" class="pd-related-btn">View <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
                <article class="pd-related-card">
                    <div class="pd-related-img-wrap">
                        <img src="{{ asset('assets/images/prod2.jpg') }}" alt="Embroidered Fleece Hoodie" loading="lazy">
                        <div class="pd-related-actions">
                            <a href="#" class="pd-related-quick-btn"><i class="bi bi-magic"></i> Customise</a>
                        </div>
                        <span class="pd-related-badge pd-related-badge--new">New In</span>
                    </div>
                    <div class="pd-related-body">
                        <span class="pd-related-cat"><i class="bi bi-tag"></i> Sports</span>
                        <h3 class="pd-related-name">Embroidered Fleece Hoodie</h3>
                        <div class="pd-related-footer">
                            <span class="pd-related-price">From £14<small>.75</small></span>
                            <a href="{{ route('product-detail') }}" class="pd-related-btn">View <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
                <article class="pd-related-card">
                    <div class="pd-related-img-wrap">
                        <img src="{{ asset('assets/images/prod3.jpg') }}" alt="Hi-Vis Safety Vest" loading="lazy">
                        <div class="pd-related-actions">
                            <a href="#" class="pd-related-quick-btn"><i class="bi bi-magic"></i> Customise</a>
                        </div>
                    </div>
                    <div class="pd-related-body">
                        <span class="pd-related-cat"><i class="bi bi-tag"></i> Safetywear</span>
                        <h3 class="pd-related-name">Hi-Vis Safety Vest</h3>
                        <div class="pd-related-footer">
                            <span class="pd-related-price">From £6<small>.50</small></span>
                            <a href="{{ route('product-detail') }}" class="pd-related-btn">View <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
                <article class="pd-related-card">
                    <div class="pd-related-img-wrap">
                        <img src="{{ asset('assets/images/prod1.jpg') }}" alt="Embroidered Baseball Cap" loading="lazy">
                        <div class="pd-related-actions">
                            <a href="#" class="pd-related-quick-btn"><i class="bi bi-magic"></i> Customise</a>
                        </div>
                        <span class="pd-related-badge pd-related-badge--sale">Sale</span>
                    </div>
                    <div class="pd-related-body">
                        <span class="pd-related-cat"><i class="bi bi-tag"></i> Accessories</span>
                        <h3 class="pd-related-name">Embroidered Baseball Cap</h3>
                        <div class="pd-related-footer">
                            <span class="pd-related-price">From £5<small>.99</small></span>
                            <a href="{{ route('product-detail') }}" class="pd-related-btn">View <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>


    <!-- ============================================================
         5. PRODUCT REVIEWS — grid layout, light theme
    ============================================================ -->
    <section id="pd-reviews">
        <div class="pd-reviews-container">

            <!-- Header -->
            <div class="pd-reviews-header">
                <div class="pd-reviews-header-left">
                    <div class="pd-rev-eyebrow">
                        <span class="pd-rev-eyebrow-dot"></span>
                        Verified Purchases
                    </div>
                    <h2 class="pd-reviews-heading">Product <span class="pd-rev-accent">Reviews</span></h2>

                    <!-- Overall score -->
                    <div class="pd-reviews-score">
                        <span class="pd-score-num">4.9</span>
                        <div class="pd-score-right">
                            <div class="pd-score-stars">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                            </div>
                            <span class="pd-score-count">Based on 127 reviews</span>
                        </div>
                    </div>
                </div>

                <!-- Write a review button -->
                <button class="pd-write-review-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    <i class="bi bi-pencil-square"></i>
                    Write a Review
                </button>
            </div>

            <!-- Reviews grid -->
            <div class="pd-reviews-grid" id="pdReviewsGrid">

                <!-- Review 1 -->
                <div class="pd-review-card">
                    <div class="pd-rev-card-top">
                        <div class="pd-rev-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <span class="pd-rev-date">18 May 2026</span>
                    </div>
                    <p class="pd-rev-body">Absolutely brilliant quality. The embroidery on our staff polos was flawless — crisp lines, perfect colour match to our brand. Turnaround was faster than expected too.</p>
                    <div class="pd-rev-author">
                        <div class="pd-rev-avatar">JT</div>
                        <div>
                            <span class="pd-rev-name">James Thornton</span>
                            <span class="pd-rev-company">Thornton Building Services</span>
                        </div>
                        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    </div>
                </div>

                <!-- Review 2 -->
                <div class="pd-review-card">
                    <div class="pd-rev-card-top">
                        <div class="pd-rev-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <span class="pd-rev-date">02 May 2026</span>
                    </div>
                    <p class="pd-rev-body">We've ordered these polos three years running for our school staff. Consistently excellent — the fabric holds up brilliantly through repeated washing and the logo stays sharp.</p>
                    <div class="pd-rev-author">
                        <div class="pd-rev-avatar">SM</div>
                        <div>
                            <span class="pd-rev-name">Sarah Mitchell</span>
                            <span class="pd-rev-company">Greenfield Academy</span>
                        </div>
                        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    </div>
                </div>

                <!-- Review 3 -->
                <div class="pd-review-card">
                    <div class="pd-rev-card-top">
                        <div class="pd-rev-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                        </div>
                        <span class="pd-rev-date">14 Apr 2026</span>
                    </div>
                    <p class="pd-rev-body">Great product for the price. Ordered 50 for our football club and every single one was consistent. The only minor note is sizing runs slightly large — order down if in doubt.</p>
                    <div class="pd-rev-author">
                        <div class="pd-rev-avatar">DK</div>
                        <div>
                            <span class="pd-rev-name">Dean Kavanagh</span>
                            <span class="pd-rev-company">Riverside FC</span>
                        </div>
                        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    </div>
                </div>

                <!-- Review 4 -->
                <div class="pd-review-card">
                    <div class="pd-rev-card-top">
                        <div class="pd-rev-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <span class="pd-rev-date">28 Mar 2026</span>
                    </div>
                    <p class="pd-rev-body">Really impressed with the whole experience. Uploaded our logo, got a proof within hours, and the finished shirts looked incredible. Our hospitality team gets compliments constantly.</p>
                    <div class="pd-rev-author">
                        <div class="pd-rev-avatar">PS</div>
                        <div>
                            <span class="pd-rev-name">Priya Sharma</span>
                            <span class="pd-rev-company">Horizon Hospitality Group</span>
                        </div>
                        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    </div>
                </div>

                <!-- Review 5 -->
                <div class="pd-review-card">
                    <div class="pd-rev-card-top">
                        <div class="pd-rev-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <span class="pd-rev-date">10 Mar 2026</span>
                    </div>
                    <p class="pd-rev-body">Kitted out 80 warehouse staff — every polo arrived on time, perfectly embroidered, and the pricing was the best we found. Communication from the team was excellent throughout.</p>
                    <div class="pd-rev-author">
                        <div class="pd-rev-avatar">ME</div>
                        <div>
                            <span class="pd-rev-name">Mark Ellison</span>
                            <span class="pd-rev-company">Ellison Logistics Ltd</span>
                        </div>
                        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    </div>
                </div>

                <!-- Review 6 -->
                <div class="pd-review-card">
                    <div class="pd-rev-card-top">
                        <div class="pd-rev-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <span class="pd-rev-date">22 Feb 2026</span>
                    </div>
                    <p class="pd-rev-body">Beautifully made polo shirts. Soft, professional and the embroidery quality is exceptional. Our salon team look incredibly smart and clients regularly comment on them.</p>
                    <div class="pd-rev-author">
                        <div class="pd-rev-avatar">LD</div>
                        <div>
                            <span class="pd-rev-name">Lauren Davies</span>
                            <span class="pd-rev-company">Luxe Hair &amp; Beauty Studio</span>
                        </div>
                        <span class="pd-rev-verified"><i class="bi bi-patch-check-fill"></i> Verified</span>
                    </div>
                </div>

            </div>
            <!-- /pd-reviews-grid -->

        </div>
    </section>


    <!-- ============================================================
         REVIEW MODAL (Bootstrap 5)
    ============================================================ -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered pd-rev-modal-dialog">
            <div class="modal-content pd-rev-modal-content">

                <!-- Modal header -->
                <div class="modal-header pd-rev-modal-header">
                    <div>
                        <h5 class="modal-title pd-rev-modal-title" id="reviewModalLabel">Write a Review</h5>
                        <p class="pd-rev-modal-sub">Classic Embroidered Polo Shirt</p>
                    </div>
                    <button type="button" class="pd-rev-modal-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="modal-body pd-rev-modal-body">

                    <!-- Star rating picker -->
                    <div class="pd-rev-form-group">
                        <label class="pd-rev-form-label">Your Rating <span class="pd-rev-required">*</span></label>
                        <div class="pd-rev-star-picker" id="pdRevStarPicker" role="group" aria-label="Star rating">
                            <button type="button" class="pd-rev-star-btn" data-val="1" aria-label="1 star"><i class="bi bi-star"></i></button>
                            <button type="button" class="pd-rev-star-btn" data-val="2" aria-label="2 stars"><i class="bi bi-star"></i></button>
                            <button type="button" class="pd-rev-star-btn" data-val="3" aria-label="3 stars"><i class="bi bi-star"></i></button>
                            <button type="button" class="pd-rev-star-btn" data-val="4" aria-label="4 stars"><i class="bi bi-star"></i></button>
                            <button type="button" class="pd-rev-star-btn" data-val="5" aria-label="5 stars"><i class="bi bi-star"></i></button>
                        </div>
                        <span class="pd-rev-star-label" id="pdRevStarLabel">Select a rating</span>
                        <input type="hidden" id="pdRevRating" value="0">
                    </div>

                    <div class="pd-rev-form-row">
                        <div class="pd-rev-form-group">
                            <label class="pd-rev-form-label" for="pdRevName">Full Name <span class="pd-rev-required">*</span></label>
                            <input type="text" id="pdRevName" class="pd-rev-form-input" placeholder="e.g. James Thornton" autocomplete="name">
                        </div>
                        <div class="pd-rev-form-group">
                            <label class="pd-rev-form-label" for="pdRevEmail">Email Address <span class="pd-rev-required">*</span></label>
                            <input type="email" id="pdRevEmail" class="pd-rev-form-input" placeholder="your@email.com" autocomplete="email">
                            <span class="pd-rev-form-hint">Not displayed publicly</span>
                        </div>
                    </div>

                    <div class="pd-rev-form-group">
                        <label class="pd-rev-form-label" for="pdRevText">Your Review <span class="pd-rev-required">*</span></label>
                        <textarea id="pdRevText" class="pd-rev-form-input pd-rev-textarea" rows="4" placeholder="Tell other customers about your experience with this product…"></textarea>
                        <span class="pd-rev-char-count"><span id="pdRevCharCount">0</span> / 500</span>
                    </div>

                    <!-- Error message -->
                    <div class="pd-rev-error" id="pdRevError" style="display:none;">
                        <i class="bi bi-exclamation-circle"></i>
                        <span id="pdRevErrorMsg">Please fill in all required fields.</span>
                    </div>

                </div>

                <!-- Modal footer -->
                <div class="modal-footer pd-rev-modal-footer">
                    <button type="button" class="pd-rev-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="pd-rev-submit-btn" id="pdRevSubmitBtn">
                        <i class="bi bi-send"></i>
                        Submit Review
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/product-detail.js') }}"></script>
@endpush
