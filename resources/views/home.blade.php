@extends('layouts.app')

@section('title', 'Threads Embroidery &amp; Print Ltd')

@section('loader')
@include('partials.loader')
@endsection

@section('content')
<!-- ========================================================
       HERO SECTION
  ======================================================== -->
    <section id="hero">

        <!-- Subtle bg gradient + grain texture -->
        <div class="hero-bg-gradient"></div>
        <div class="hero-grain" aria-hidden="true"></div>

        <div class="hero-container">
            <div class="row g-0 align-items-center hero-row">

                <!-- LEFT: Text Content -->
                <div class="col-lg-6 hero-left">

                    <div class="hero-eyebrow">
                        <span class="eyebrow-dot"></span>
                        UK's Trusted Custom Apparel Partner
                    </div>

                    <h1 class="hero-headline">
                        Your Brand, Stitched Into
                        <span class="headline-accent"> Every Thread.</span>
                    </h1>

                    <p class="hero-body">
                        From embroidered corporate uniforms to printed sportswear — precision, quality &amp; speed for
                        businesses, schools &amp; sports teams across the UK.
                    </p>

                    <div class="hero-actions">
                        <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="btn hero-primary-btn">
                            Browse Catalogue
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('contact') }}" class="hero-ghost-btn">
                            <span class="play-wrap"><i class="bi bi-play-fill"></i></span>
                            Get a Free Quote
                        </a>
                    </div>

                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-num" data-target="15" data-suffix="+">15+</span>
                            <span class="stat-label">Years Experience</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <span class="stat-num" data-target="5000" data-suffix="+">5,000+</span>
                            <span class="stat-label">Happy Clients</span>
                        </div>
                        <div class="stat-divider"></div>
                        <div class="stat-item">
                            <span class="stat-num">7–10</span>
                            <span class="stat-label">Day Turnaround</span>
                        </div>
                    </div>

                </div>
                <!-- END hero-left -->


                <!-- RIGHT: Image Collage -->
                <div class="col-lg-6 hero-right">
                    <div class="hero-image-collage">

                        <!-- Main large image -->
                        <div class="hero-img-main">
                            <img src="{{ asset('assets/images/about1.jpg') }}"
                                alt="Custom embroidered polo shirts" loading="eager" />
                            <!-- Overlay badge -->
                            <div class="img-badge-uk">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>UK Based</span>
                            </div>
                        </div>

                        <!-- Top-right stacked image -->
                        <div class="hero-img-top-right">
                            <img src="{{ asset('assets/images/about2.jpg') }}"
                                alt="Branded corporate uniforms" loading="eager" />
                        </div>

                        <!-- Bottom-right stacked image -->
                        <div class="hero-img-bottom-right">
                            <video src="{{ asset('assets/images/herovideo.mp4') }}" autoplay muted loop playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                        </div>

                        <!-- Floating info card -->
                        <div class="hero-float-card">
                            <div class="hfc-icon"><i class="bi bi-patch-check-fill"></i></div>
                            <div class="hfc-text">
                                <strong>Premium Quality</strong>
                                <span>Embroidery &amp; Print</span>
                            </div>
                        </div>

                        <!-- Floating delivery badge -->
                        <div class="hero-delivery-badge">
                            <i class="bi bi-calendar-check"></i>
                            <div>
                                <strong>7–10</strong>
                                <span>Days</span>
                            </div>
                        </div>

                        <!-- Decorative dot grid -->
                        <div class="dot-grid"></div>

                    </div>
                </div>
                <!-- END hero-right -->

            </div>
        </div>

        <!-- Bottom stitch line -->
        <div class="hero-stitch-bottom">
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
            <span></span><span></span><span></span><span></span><span></span>
        </div>

        <!-- Scroll cue -->
        <div class="hero-scroll-hint">
            <div class="scroll-mouse">
                <div class="scroll-dot"></div>
            </div>
            <span>Scroll</span>
        </div>

    </section>
    <!-- END HERO -->




    <!-- ========================================================
     ABOUT US SECTION
     Add this directly after the closing </section> of #hero
======================================================== -->
    <section id="about">
        <div class="about-container">

            <!-- ── LEFT: Image Stack ── -->
            <div class="about-images">

                <div class="about-img-main">
                    <img src="{{ asset('assets/images/aboutmain.jpg') }}" alt="Threads Embroidery team at work" loading="lazy" />
                    <!-- Floating years badge -->
                    <div class="about-years-badge">
                        <span class="ayb-num">15+</span>
                        <span class="ayb-label">Years of<br>Excellence</span>
                    </div>
                </div>

                <div class="about-img-secondary">
                    <img src="{{ asset('assets/images/about-2.jpeg') }}" alt="Custom embroidery close-up" loading="lazy" />
                </div>

                <!-- Decorative elements -->
                <div class="about-dot-grid"></div>
                <div class="about-ring"></div>

            </div>
            <!-- END about-images -->


            <!-- ── RIGHT: Text Content ── -->
            <div class="about-content">

                <div class="about-eyebrow">
                    <span class="about-eyebrow-dot"></span>
                    Who We Are
                </div>

                <h2 class="about-headline">
                    Built on Trust.<br>
                    <span class="about-headline-accent">Defined by Quality.</span>
                </h2>

                <p class="about-lead">
                    At Threads Embroidery &amp; Print, we are more than just a uniform supplier — we are a dedicated
                    branding partner for businesses, schools, and sports teams right across the United Kingdom. Our
                    mission is straightforward: deliver exceptional custom embroidery and printing services that
                    represent your brand with pride, every single time.
                </p>

                <p class="about-body">
                    Founded on a passion for precision and an unwavering commitment to craftsmanship, everything we do
                    is centred around your needs. Whether you require a single embroidered polo shirt or a full-scale
                    uniform programme for hundreds of staff, we approach every project — large or small — with the same
                    meticulous attention to detail and dedication to getting it right. No shortcuts. No compromises.
                </p>

                <!-- Pillars -->
                <div class="about-pillars">
                    <div class="about-pillar">
                        <div class="pillar-icon"><i class="bi bi-shield-fill-check"></i></div>
                        <div class="pillar-text">
                            <strong>Trust &amp; Reliability</strong>
                            <span>Consistent quality you can count on, order after order.</span>
                        </div>
                    </div>
                    <div class="about-pillar">
                        <div class="pillar-icon"><i class="bi bi-gem"></i></div>
                        <div class="pillar-text">
                            <strong>Precision Craftsmanship</strong>
                            <span>Every stitch and print executed with exacting care.</span>
                        </div>
                    </div>
                </div>

                <div class="about-actions">
                    <a href="{{ route('about') }}" class="btn about-primary-btn">Meet the Team <i class="bi bi-arrow-right ms-2"></i></a>
                    <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="about-link-btn">Our Products <i class="bi bi-arrow-right ms-1"></i></a>
                </div>

            </div>
            <!-- END about-content -->

        </div>
    </section>
    <!-- END ABOUT US SECTION -->





    <!-- ============================================================
     PRODUCT CUSTOMIZER SECTION  —  customizer-section.html
     Link in <head>: <link rel="stylesheet" href="customizer-section.css">
     Link before </body>: <script src="customizer-section.js"></script>
     ============================================================ -->

    <section id="customizer">
        <div class="customizer-container">

            <!-- ── LEFT: Text column ── -->
            <div class="cust-text" data-reveal>

                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    Live Product Customizer
                </div>

                <h2 class="cust-heading">
                    Design It. <span class="accent">See It Live.</span>
                </h2>

                <p class="cust-lead">
                    Our product customizer lets you select fabric, pick product and colours, upload your logo, &
                    watch it appear on the item in real time.
                </p>

                <!-- Feature pills -->
                <ul class="cust-features">
                    <li class="cust-feat" data-reveal-item>
                        <span class="feat-icon"><i class="bi bi-palette2"></i></span>
                        <div>
                            <strong>Choose Fabric &amp; Colour</strong>
                            <span>Browse the full range of garments, materials and colourways</span>
                        </div>
                    </li>
                    <li class="cust-feat" data-reveal-item>
                        <span class="feat-icon"><i class="bi bi-upload"></i></span>
                        <div>
                            <strong>Upload Your Logo / Artwork</strong>
                            <span>Upload your design files in PNG, JPG or JPEG format</span>
                        </div>
                    </li>
                    <li class="cust-feat" data-reveal-item>
                        <span class="feat-icon"><i class="bi bi-arrows-move"></i></span>
                        <div>
                            <strong>Resize &amp; Position Live</strong>
                            <span>Drag, scale and rotate your design directly on the garment</span>
                        </div>
                    </li>
                    <li class="cust-feat" data-reveal-item>
                        <span class="feat-icon"><i class="bi bi-eye"></i></span>
                        <div>
                            <strong>Real-Time Preview</strong>
                            <span>Instant photorealistic render before you add to cart</span>
                        </div>
                    </li>
                </ul>

                <!-- CTA -->
                <div class="cust-cta-wrap">

                    <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="cust-btn-primary">
                        <span class="btn-inner">
                            <i class="bi bi-magic"></i>
                            Start Customising Your Product
                        </span>
                        <span class="btn-glow"></span>
                    </a>
                </div>

            </div><!-- /cust-text -->

            <!-- ── RIGHT: Mockup / Visual column ── -->
            <div class="cust-visual" data-reveal>

                <!-- Main mockup card -->
                <div class="cust-mockup-card">

                    <!-- Fake browser / app chrome -->
                    <div class="mockup-chrome">
                        <span class="chrome-dot red"></span>
                        <span class="chrome-dot amber"></span>
                        <span class="chrome-dot green"></span>
                        <span class="chrome-url">threadsembroideryprint.co.uk / customiser</span>
                    </div>

                    <!-- Mockup body -->
                    <div class="mockup-body">

                        <!-- Left: product selector panel -->
                        <div class="mock-panel mock-panel--left">
                            <p class="mock-panel-title">Product</p>
                            <div class="mock-swatch-row">
                                <div class="mock-swatch active" style="background:#1a1a2e;" title="Navy"></div>
                                <div class="mock-swatch" style="background:#2c3e50;" title="Slate"></div>
                                <div class="mock-swatch" style="background:#ee6023;" title="Orange"></div>
                                <div class="mock-swatch" style="background:#f5f5f5;" title="White"></div>
                                <div class="mock-swatch" style="background:#2d6a4f;" title="Forest"></div>
                            </div>
                            <div class="mock-size-row">
                                <span class="mock-size">XS</span>
                                <span class="mock-size active">M</span>
                                <span class="mock-size">L</span>
                                <span class="mock-size">XL</span>
                                <span class="mock-size">2XL</span>
                            </div>
                            <div class="mock-upload-zone">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <span>Drop logo here</span>
                            </div>
                        </div>

                        <!-- Centre: t-shirt preview -->
                        <div class="mock-preview">
                            <div class="mock-tshirt-wrap">

                                <!-- T-shirt SVG silhouette -->
                                <svg class="mock-tshirt-svg" viewBox="0 0 220 240" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="tshirtGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#1a1a2e" />
                                            <stop offset="100%" style="stop-color:#0f0f1a" />
                                        </linearGradient>
                                        <filter id="tshirtShadow">
                                            <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#000"
                                                flood-opacity="0.5" />
                                        </filter>
                                    </defs>
                                    <path class="tshirt-body"
                                        d="M55,20 L20,55 L50,70 L45,220 L175,220 L170,70 L200,55 L165,20 C155,35 140,42 110,42 C80,42 65,35 55,20 Z"
                                        fill="url(#tshirtGrad)" filter="url(#tshirtShadow)"
                                        stroke="rgba(255,255,255,0.06)" stroke-width="1" />
                                </svg>

                                <!-- Animated logo overlay on t-shirt -->
                                <div class="mock-logo-on-shirt">
                                    <div class="mock-logo-box">
                                        <!-- Simplified 'T' logo in brand colours -->
                                        <svg viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"
                                            class="mock-logo-svg">
                                            <rect x="8" y="10" width="44" height="8" rx="2" fill="#ee6023" />
                                            <rect x="23" y="18" width="14" height="32" rx="2" fill="#ffffff" />
                                        </svg>
                                        <!-- Resize handle dots -->
                                        <span class="resize-handle tl"></span>
                                        <span class="resize-handle tr"></span>
                                        <span class="resize-handle bl"></span>
                                        <span class="resize-handle br"></span>
                                    </div>
                                </div>

                            </div>

                            <!-- Cursor interaction hint -->
                            <div class="mock-cursor" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 4L10.5 20L13 13L20 10.5L4 4Z" fill="white" stroke="white"
                                        stroke-width="1" stroke-linejoin="round" />
                                </svg>
                            </div>

                        </div><!-- /mock-preview -->

                        <!-- Right panel: customisation options -->
                        <div class="mock-panel mock-panel--right">
                            <p class="mock-panel-title">Customise</p>
                            <div class="mock-option-row">
                                <span class="mock-opt-label">Method</span>
                                <span class="mock-opt-val active">Embroidery</span>
                            </div>
                            <div class="mock-option-row">
                                <span class="mock-opt-label">Position</span>
                                <span class="mock-opt-val">Left Chest</span>
                            </div>
                            <div class="mock-option-row">
                                <span class="mock-opt-label">Size</span>
                                <span class="mock-opt-val">80mm</span>
                            </div>
                            <div class="mock-divider"></div>
                            <div class="mock-qty-row">
                                <span class="mock-opt-label">Qty</span>
                                <div class="mock-qty-ctrl">
                                    <span>−</span>
                                    <strong>25</strong>
                                    <span>+</span>
                                </div>
                            </div>
                            <div class="mock-price-row">
                                <span class="mock-price-label">Est. Total</span>
                                <span class="mock-price-val">£187<small>.50</small></span>
                            </div>
                            <div class="mock-add-btn">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </div>
                        </div>

                    </div><!-- /mockup-body -->
                </div><!-- /cust-mockup-card -->

                <!-- Floating badges -->
                <div class="cust-badge cust-badge--tl" data-float="slow">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Live Preview
                </div>

                <div class="cust-badge cust-badge--br" data-float="fast">
                    <i class="bi bi-patch-check-fill"></i>
                    Free Artwork Setup
                </div>

                <!-- Decorative elements -->
                <div class="cust-dot-grid" aria-hidden="true">
                    <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="dotpat2" x="0" y="0" width="15" height="15" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1.2" fill="rgba(238,96,35,0.25)" />
                            </pattern>
                        </defs>
                        <rect width="120" height="120" fill="url(#dotpat2)" />
                    </svg>
                </div>

                <!-- Spinning ring accent -->
                <div class="cust-ring" aria-hidden="true"></div>

            </div><!-- /cust-visual -->

        </div><!-- /customizer-container -->

        <!-- Bottom stitch line (matches other sections) -->
        <div class="cust-stitch-line" aria-hidden="true">
            <svg viewBox="0 0 1440 12" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <line x1="0" y1="6" x2="1440" y2="6" stroke="rgba(238,96,35,0.18)" stroke-width="2"
                    stroke-dasharray="12 8" />
            </svg>
        </div>

    </section>

    @include('partials.branding-service')


    <!-- ============================================================
     CATALOGUE SECTION  —  catalogue-section.html
     Full-width slider — NO wrapping container
     Overlay style: text + button sit ON TOP of image at the bottom
     Link in <head>: <link rel="stylesheet" href="catalogue-section.css">
     Link before </body>: <script src="catalogue-section.js"></script>
     ============================================================ -->

    <section id="catalogue">

        <!-- ── Section header ── -->
        <div class="cat-header">
            <div class="cat-header-inner">
                <div class="cat-eyebrow">
                    <span class="cat-eyebrow-dot"></span>
                    Browse Categories
                </div>
                <div class="cat-header-row">
                    <div class="cat-header-text">
                        <h2 class="cat-heading">
                            Explore Our Full <span class="cat-accent">Catalogue</span>
                        </h2>
                        <p class="cat-subhead">
                            From workwear to schoolwear — every garment customisable with your brand.
                        </p>
                    </div>
                    <div class="cat-nav-arrows">
                        <button class="cat-arrow-btn cat-arrow-prev" aria-label="Previous slide">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <button class="cat-arrow-btn cat-arrow-next" aria-label="Next slide">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Slider viewport ── -->
        <div class="cat-slider-viewport" id="catViewport">
            <div class="cat-slider-track" id="catTrack">
                @foreach(array_slice($catalogCategories ?? [], 0, 8) as $index => $cat)
                    @php $catImage = asset('assets/images/cat' . (($index % 6) + 1) . '.jpg'); @endphp
                    <div class="cat-card">
                        <img src="{{ $catImage }}" alt="{{ $cat['name'] }}" loading="lazy" draggable="false">
                        @if($index === 0)
                            <span class="cat-card-tag">Popular</span>
                        @elseif($index === 2)
                            <span class="cat-card-tag">Trending</span>
                        @endif
                        <div class="cat-card-overlay">
                            <div class="cat-card-overlay-top">
                                <span class="cat-card-count"><i class="bi bi-grid-3x3-gap"></i> {{ $cat['count'] }} Products</span>
                            </div>
                            <div class="cat-card-overlay-bottom">
                                <h3 class="cat-card-title">{{ $cat['name'] }}</h3>
                                <p class="cat-card-desc">Browse our {{ strtolower($cat['name']) }} range — fully customisable with your brand.</p>
                                <a href="{{ route('shop.category', $cat['slug']) }}" class="cat-card-btn">
                                    Browse Category <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div><!-- /cat-slider-track -->
        </div><!-- /cat-slider-viewport -->

        <!-- ── Dots ── -->
        <div class="cat-dots" id="catDots" role="tablist" aria-label="Catalogue slides"></div>

        <!-- ── Footer CTA ── -->
        <div class="cat-footer">
            <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="cat-view-all-btn">
                View Full Catalogue
                <span class="cat-view-all-icon"><i class="bi bi-arrow-up-right"></i></span>
            </a>
        </div>

    </section>







    <!-- ============================================================
     FEATURED PRODUCTS SECTION  —  featured-products.html
     3 products in a row, light theme
     Link in <head>: <link rel="stylesheet" href="featured-products.css">
     ============================================================ -->

    <section id="featured-products">
        <div class="fp-container">

            <!-- ── Header ── -->
            <div class="fp-header" data-fp-reveal>
                <div class="fp-header-left">
                    <div class="fp-eyebrow">
                        <span class="fp-eyebrow-dot"></span>
                        Hand-Picked For You
                    </div>
                    <h2 class="fp-heading">
                        Featured <span class="fp-accent">Products</span>
                    </h2>
                    <p class="fp-subhead">
                        A selection of our most popular branded garments — every one fully customisable with your logo.
                    </p>
                </div>
                <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="fp-view-all">
                    View All Products
                    <span class="fp-view-all-icon"><i class="bi bi-arrow-up-right"></i></span>
                </a>
            </div>

            <!-- ── Products grid ── -->
            <div class="fp-grid">
                @foreach(($featuredProducts ?? []) as $index => $item)
                    @php
                        $fpWhole = floor($item['min_price']);
                        $fpDec = str_pad((string) round(($item['min_price'] - $fpWhole) * 100), 2, '0', STR_PAD_LEFT);
                    @endphp
                    <article class="fp-card {{ $index === 1 ? 'fp-card--featured' : '' }}" data-fp-reveal data-fp-delay="{{ $index }}">
                        <div class="fp-card-img-wrap">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" draggable="false">
                            <div class="fp-card-actions">
                                <a href="{{ $item['url'] }}" class="fp-quick-btn fp-quick-customize">
                                    <i class="bi bi-magic"></i> Customise Now
                                </a>
                            </div>
                            @if($item['is_new'])
                                <span class="fp-badge fp-badge--new">New In</span>
                            @elseif($index === 1)
                                <span class="fp-badge fp-badge--featured">Featured</span>
                            @elseif($index === 0)
                                <span class="fp-badge fp-badge--bestseller">Best Seller</span>
                            @else
                                <span class="fp-badge fp-badge--popular">Popular</span>
                            @endif
                        </div>
                        <div class="fp-card-body">
                            <div class="fp-card-meta-row">
                                <span class="fp-card-category">
                                    <i class="bi bi-tag"></i> {{ $item['category_name'] }}
                                </span>
                                <span class="fp-card-rating">
                                    <i class="bi bi-star-fill"></i> 4.9
                                </span>
                            </div>
                            <h3 class="fp-card-name">{{ $item['title'] }}</h3>
                            <div class="fp-card-footer">
                                <div class="fp-card-price-wrap">
                                    <span class="fp-price-from">From</span>
                                    <span class="fp-price">£{{ $fpWhole }}<small>.{{ $fpDec }}</small></span>
                                    <span class="fp-price-note">per unit</span>
                                </div>
                                <a href="{{ $item['url'] }}" class="fp-card-btn">
                                    <i class="bi bi-magic"></i>
                                    Customise Now
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div><!-- /fp-grid -->

            <!-- Trust strip -->
            <div class="fp-trust-strip" data-fp-reveal>
                <div class="fp-trust-item">
                    <i class="bi bi-truck"></i> Free UK Delivery
                </div>
                <div class="fp-trust-divider"></div>
                <div class="fp-trust-item">
                    <i class="bi bi-patch-check-fill"></i> Quality Guaranteed
                </div>
                <div class="fp-trust-divider"></div>
                <div class="fp-trust-item">
                    <i class="bi bi-brush"></i> Free Artwork Setup
                </div>
                <div class="fp-trust-divider"></div>
                <div class="fp-trust-item">
                    <i class="bi bi-shield-check"></i> Secure Checkout
                </div>
            </div>

        </div><!-- /fp-container -->
    </section>



    <!-- ============================================================
     VIDEO SECTION  —  video-section.html
     Left: autoplaying muted video · Right: heading + content + CTA
     Link in <head>: <link rel="stylesheet" href="video-section.css">
     ============================================================ -->

    <section id="video-section">
        <div class="vs-container">

            <!-- ── Left: Video ── -->
            <div class="vs-video-col" data-vs-reveal="left">
                <div class="vs-video-wrap">
                    <video src="{{ asset('assets/images/video.mp4') }}" autoplay muted loop playsinline preload="metadata"
                        aria-label="Threads Embroidery & Print — brand video">
                    </video>
                    <!-- Thin orange frame accent -->
                    <div class="vs-video-frame" aria-hidden="true"></div>
                </div>
            </div>

            <!-- ── Right: Text ── -->
            <div class="vs-text-col" data-vs-reveal="right">

                <div class="vs-eyebrow">
                    <span class="vs-eyebrow-dot"></span>
                    See Us In Action
                </div>

                <h2 class="vs-heading">
                    Craftsmanship You Can <span class="vs-accent">See &amp; Feel</span>
                </h2>

                <p class="vs-lead">
                    Every stitch and every print is the result of over 15 years of expertise. We work with businesses,
                    schools and sports clubs across the UK — delivering branded garments that are built to last and
                    designed to impress.
                </p>

                <ul class="vs-points">
                    <li>
                        <span class="vs-point-icon"><i class="bi bi-check2"></i></span>
                        State-of-the-art embroidery and print machinery
                    </li>
                    <li>
                        <span class="vs-point-icon"><i class="bi bi-check2"></i></span>
                        In-house artwork team — free setup on every order
                    </li>
                    <li>
                        <span class="vs-point-icon"><i class="bi bi-check2"></i></span>
                        7–10 working day turnaround with free UK delivery
                    </li>
                    <li>
                        <span class="vs-point-icon"><i class="bi bi-check2"></i></span>
                        Trusted by 5,000+ clients nationwide
                    </li>
                </ul>

                <a href="{{ route('contact') }}" class="vs-cta-btn">
                    Get a Free Quote
                    <i class="bi bi-arrow-right"></i>
                </a>

            </div>

        </div>
    </section>

@endsection

@section('after_content')
@include('partials.testimonials')
@include('partials.gallery')
@endsection
