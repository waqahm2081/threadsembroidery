@extends('layouts.app')

@section('title', 'Threads Embroidery &amp; Print Ltd')

@section('loader')
@include('partials.loader')
@endsection

@section('content')
<!-- ========================================================
       MAIN BANNER — Video hero (screenshot layout)
  ======================================================== -->
    <section id="main-banner" aria-label="Threads Embroidery main banner">

        <!-- Background video -->
        <div class="mb-video-wrap" aria-hidden="true">
            <video src="{{ asset('assets/images/herovideo.mp4') }}" autoplay muted loop playsinline preload="auto"></video>
            <div class="mb-video-overlay"></div>
        </div>

        <!-- Auto-sliding feature pills -->
        <div class="mb-features-ticker" aria-hidden="true">
            <div class="mb-features-track">
                @php
                    $mbFeatures = [
                        ['icon' => 'bi-scissors', 'title' => 'Embroidery', 'sub' => 'Left Chest & Back'],
                        ['icon' => 'bi-printer', 'title' => 'Screen Printing', 'sub' => 'Vibrant Colours'],
                        ['icon' => 'bi-briefcase', 'title' => 'Workwear', 'sub' => 'Corporate Uniforms'],
                        ['icon' => 'bi-mortarboard', 'title' => 'Schoolwear', 'sub' => 'Blazers & PE Kits'],
                        ['icon' => 'bi-trophy', 'title' => 'Sportswear', 'sub' => 'Teams & Clubs'],
                        ['icon' => 'bi-patch-check', 'title' => 'Custom Patches', 'sub' => 'Woven & Embroidered'],
                        ['icon' => 'bi-droplet', 'title' => 'DTF Print', 'sub' => 'Full Colour'],
                        ['icon' => 'bi-magic', 'title' => 'Live Customizer', 'sub' => 'Design Online'],
                    ];
                    $mbFeaturesLoop = array_merge($mbFeatures, $mbFeatures);
                @endphp
                @foreach($mbFeaturesLoop as $feat)
                    <div class="mb-feature-pill">
                        <span class="mb-feature-icon"><i class="bi {{ $feat['icon'] }}"></i></span>
                        <span class="mb-feature-text">
                            <strong>{{ $feat['title'] }}</strong>
                            <span>{{ $feat['sub'] }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-content">

            <!-- Trust badge -->
            <div class="mb-trust-badge">
                <span class="mb-trust-rating"><i class="bi bi-star-fill"></i> 4.9</span>
                <span class="mb-trust-sep">|</span>
                <span>5,000+ Clients</span>
                <span class="mb-trust-sep">|</span>
                <span>UK Nationwide</span>
                <span class="mb-trust-sep">|</span>
                <span class="mb-trust-highlight">Free Artwork Setup</span>
            </div>

            <!-- Headline -->
            <h1 class="mb-headline">
                Your Brand, Stitched Into
                <span class="mb-headline-accent">Every Thread.</span>
            </h1>

            <!-- CTAs -->
            <div class="mb-actions">
                <a href="{{ route('login') }}" class="mb-btn mb-btn-primary">
                    Sign In / Register
                    <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="mb-btn mb-btn-outline">
                    Browse Catalogue
                    <i class="bi bi-grid-3x3-gap"></i>
                </a>
            </div>

            <p class="mb-subtext">
                <span><i class="bi bi-check2"></i> Free quote</span>
                <span class="mb-subtext-dot">•</span>
                <span><i class="bi bi-check2"></i> Free artwork setup</span>
                <span class="mb-subtext-dot">•</span>
                <span><i class="bi bi-check2"></i> 10–14 day turnaround</span>
            </p>

            <!-- Stats grid -->
            <div class="mb-stats-grid">
                <div class="mb-stat-card">
                    <i class="bi bi-star mb-stat-icon"></i>
                    <strong class="mb-stat-value">4.9/5</strong>
                    <span class="mb-stat-label">500+ happy clients</span>
                </div>
                <div class="mb-stat-card">
                    <i class="bi bi-file-earmark-check mb-stat-icon"></i>
                    <strong class="mb-stat-value">5,000+</strong>
                    <span class="mb-stat-label">Orders completed</span>
                </div>
                <div class="mb-stat-card">
                    <i class="bi bi-clock mb-stat-icon"></i>
                    <strong class="mb-stat-value">10–14</strong>
                    <span class="mb-stat-label">Day turnaround</span>
                </div>
                <div class="mb-stat-card">
                    <i class="bi bi-geo-alt mb-stat-icon"></i>
                    <strong class="mb-stat-value">UK</strong>
                    <span class="mb-stat-label">Nationwide delivery</span>
                </div>
                <div class="mb-stat-card">
                    <i class="bi bi-shield-check mb-stat-icon"></i>
                    <strong class="mb-stat-value">100%</strong>
                    <span class="mb-stat-label">Quality guaranteed</span>
                </div>
                <div class="mb-stat-card">
                    <i class="bi bi-brush mb-stat-icon"></i>
                    <strong class="mb-stat-value">Free</strong>
                    <span class="mb-stat-label">Artwork setup</span>
                </div>
            </div>

        </div>
    </section>
    <!-- END MAIN BANNER -->




    <!-- ========================================================
     ABOUT US SECTION
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

    <!-- ============================================================
     ORDER PROCESS SECTION — light theme, connected flow
     ============================================================ -->
    <section id="order-process" aria-labelledby="op-heading">
        <div class="op-container">

            <div class="op-header" data-op-reveal>
                <div class="op-eyebrow">
                    <span class="op-eyebrow-dot"></span>
                    How It Works
                </div>
                <h2 class="op-heading" id="op-heading">
                    Order in Minutes, <span class="op-accent">Delivered Fast</span>
                </h2>
                <p class="op-subhead">
                    From live customisation to your doorstep — a clear four-step journey trusted by 5,000+ UK businesses, schools and sports clubs.
                </p>
            </div>

            <div class="op-flow" data-op-reveal>
                <!-- Connecting progress track -->
                <div class="op-track" aria-hidden="true">
                    <div class="op-track-line"></div>
                    <div class="op-track-fill"></div>
                </div>

                <div class="op-steps">
                    @php
                        $processSteps = [
                            [
                                'num' => '01',
                                'icon' => 'bi-magic',
                                'title' => 'Choose & Customise',
                                'desc' => 'Browse our catalogue, pick your garment and colours, then use our live customizer to preview your logo on the product.',
                            ],
                            [
                                'num' => '02',
                                'icon' => 'bi-cloud-arrow-up',
                                'title' => 'Upload Your Logo',
                                'desc' => 'Add your artwork with placement and size details. Our team reviews every file — free artwork setup included.',
                            ],
                            [
                                'num' => '03',
                                'icon' => 'bi-scissors',
                                'title' => 'We Brand Your Order',
                                'desc' => 'Your garments are embroidered or printed in-house with precision machinery, then quality-checked before dispatch.',
                            ],
                            [
                                'num' => '04',
                                'icon' => 'bi-truck',
                                'title' => 'Delivered to You',
                                'desc' => 'Free UK delivery straight to your door. Typical turnaround is 10–14 working days from order confirmation.',
                            ],
                        ];
                    @endphp

                    @foreach($processSteps as $index => $step)
                        <article class="op-step" data-op-step style="--op-delay: {{ $index * 0.12 }}s">
                            <div class="op-node-wrap">
                                <div class="op-node">
                                    <span class="op-node-num">{{ $step['num'] }}</span>
                                </div>
                                @if($index < count($processSteps) - 1)
                                    <div class="op-connector" aria-hidden="true">
                                        <span class="op-connector-line"></span>
                                        <span class="op-connector-arrow"><i class="bi bi-chevron-right"></i></span>
                                    </div>
                                @endif
                            </div>

                            <div class="op-card">
                                <span class="op-card-step">Step {{ $step['num'] }}</span>
                                <div class="op-card-icon">
                                    <i class="bi {{ $step['icon'] }}"></i>
                                </div>
                                <h3 class="op-card-title">{{ $step['title'] }}</h3>
                                <p class="op-card-desc">{{ $step['desc'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="op-cta-wrap" data-op-reveal>
                <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="op-cta-btn">
                    <i class="bi bi-bag-plus"></i>
                    Start Your First Order
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

        </div>
    </section>

    <!-- ============================================================
     STACK SHOWCASE — 3 full-width scroll-stacking cards
     ============================================================ -->
    <section id="stack-showcase" aria-label="Featured highlights">
        <div class="ssc-stack">

            @php
                $sscCards = [
                    [
                        'image' => asset('assets/images/scrolling1.png'),
                        'image_alt' => 'Motocross racer wearing custom branded racing jersey',
                        'watermark' => 'CUSTOM',
                        'title' => 'Design It.',
                        'accent' => 'See It Live.',
                        'quote' => 'Preview your logo on garments in real time.',
                        'body' => 'Pick your product, choose colours, upload artwork and watch it appear on the garment instantly — before you add to cart.',
                        'cta' => 'Start Customising',
                        'cta_route' => 'shop.category',
                        'tag1' => 'Live Preview',
                        'tag2' => 'Your Logo',
                        'rating' => '4.9',
                        'reviews' => '500+',
                    ],
                    [
                        'image' => asset('assets/images/scrolling2.png'),
                        'image_alt' => 'Hospitality staff wearing embroidered denim aprons',
                        'watermark' => 'STITCH',
                        'title' => 'Precision Craft.',
                        'accent' => 'Built to Last.',
                        'quote' => 'Every stitch optimised for clarity, density and durability.',
                        'body' => 'In-house embroidery and print with professional machinery — crisp detail that holds up wash after wash, order after order.',
                        'cta' => 'Browse Workwear',
                        'cta_route' => 'shop.category',
                        'tag1' => 'Embroidery',
                        'tag2' => 'Premium Finish',
                        'rating' => '4.9',
                        'reviews' => '5,000+',
                    ],
                    [
                        'image' => asset('assets/images/scrolling3.png'),
                        'image_alt' => 'Golfer wearing embroidered sports vest and cap',
                        'watermark' => 'BRAND',
                        'title' => 'Your Team.',
                        'accent' => 'Your Identity.',
                        'quote' => 'Uniforms and kit that make your organisation stand out.',
                        'body' => 'From corporate workwear to school and sports kit — we help teams across the UK look unified, professional and proud.',
                        'cta' => 'Get a Free Quote',
                        'cta_route' => 'contact',
                        'tag1' => 'Workwear',
                        'tag2' => 'School & Sport',
                        'rating' => '4.9',
                        'reviews' => '100+',
                    ],
                ];
            @endphp

            @foreach($sscCards as $index => $card)
                <article class="ssc-card" data-ssc-card style="--ssc-i: {{ $index }}">
                    <div class="ssc-card-inner">
                        <div class="ssc-watermark" aria-hidden="true" style="--ssc-chars: {{ strlen($card['watermark']) }}">{{ $card['watermark'] }}</div>

                        <div class="ssc-left">
                            <h3 class="ssc-title">
                                {{ $card['title'] }}
                                <span class="ssc-title-accent">{{ $card['accent'] }}</span>
                            </h3>

                            <div class="ssc-quote-wrap">
                                <p class="ssc-quote">&ldquo;{{ $card['quote'] }}&rdquo;</p>
                                <img src="{{ asset('assets/images/arrow-01.png') }}" alt="" class="ssc-quote-arrow" aria-hidden="true" draggable="false">
                            </div>

                            @if($card['cta_route'] === 'contact')
                                <a href="{{ route('contact') }}" class="ssc-cta">
                                    {{ $card['cta'] }}
                                    <i class="bi bi-arrow-up-right"></i>
                                </a>
                            @else
                                <a href="{{ route('shop.category', $defaultCategorySlug ?? 'workwear') }}" class="ssc-cta">
                                    {{ $card['cta'] }}
                                    <i class="bi bi-arrow-up-right"></i>
                                </a>
                            @endif
                        </div>

                        <div class="ssc-visual">
                            <img src="{{ $card['image'] }}" alt="{{ $card['image_alt'] }}" loading="lazy" draggable="false">
                            <span class="ssc-tag ssc-tag--1"><i class="bi bi-cursor"></i> {{ $card['tag1'] }}</span>
                            <span class="ssc-tag ssc-tag--2"><i class="bi bi-cursor"></i> {{ $card['tag2'] }}</span>
                        </div>

                        <div class="ssc-right">
                            <p class="ssc-body">{{ $card['body'] }}</p>
                            <div class="ssc-rating">
                                <span class="ssc-rating-score">{{ $card['rating'] }}</span>
                                <span class="ssc-rating-sep">/</span>
                                <span class="ssc-rating-count">{{ $card['reviews'] }} Reviews</span>
                                <span class="ssc-stars" aria-hidden="true">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </span>
                            </div>
                        </div>

                        <div class="ssc-fav" aria-hidden="true">
                            <i class="bi bi-heart"></i>
                        </div>
                    </div>
                </article>
            @endforeach

        </div>
    </section>

    <!-- ============================================================
     BEFORE / AFTER SECTION — drag-to-reveal comparison
     ============================================================ -->
    <section id="before-after" aria-labelledby="ba-heading">
        <div class="ba-container">

            <div class="ba-content" data-ba-reveal="left">
                <div class="ba-eyebrow">
                    <span class="ba-eyebrow-dot"></span>
                    Before &amp; After
                </div>

                <h2 class="ba-heading" id="ba-heading">
                    Plain Garment.<br>
                    <span class="ba-accent">Professional Brand.</span>
                </h2>

                <div class="ba-points-card">
                    <h3 class="ba-points-title">Why Our Embroidery Wins</h3>

                    <ul class="ba-points">
                        <li class="ba-point">
                            <span class="ba-point-icon" aria-hidden="true">🧵</span>
                            <div class="ba-point-text">
                                <strong>Clean Stitch Paths</strong>
                                <p>Every stitch optimised for your fabric and garment type — no uneven edges, puckering or gaps around your logo.</p>
                            </div>
                        </li>
                        <li class="ba-point">
                            <span class="ba-point-icon" aria-hidden="true">🔤</span>
                            <div class="ba-point-text">
                                <strong>Sharp Small Text</strong>
                                <p>Legible lettering and fine detail on polos, caps and workwear — crisp results even on smaller branding areas.</p>
                            </div>
                        </li>
                        <li class="ba-point">
                            <span class="ba-point-icon" aria-hidden="true">⚡</span>
                            <div class="ba-point-text">
                                <strong>Production-Ready</strong>
                                <p>Durable thread, efficient density and secure placement that holds up wash after wash, order after order.</p>
                            </div>
                        </li>
                        <li class="ba-point">
                            <span class="ba-point-icon" aria-hidden="true">🎯</span>
                            <div class="ba-point-text">
                                <strong>Perfect Registration</strong>
                                <p>Precise logo placement and colour alignment on every garment in your run — consistent from the first piece to the last.</p>
                            </div>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="ba-visual" data-ba-reveal="right">
                <div class="ba-comparison" id="baComparison" aria-label="Before and after embroidery comparison. Drag to reveal.">
                    <img
                        class="ba-img ba-img-after"
                        src="{{ asset('assets/images/ba-after.png') }}"
                        alt="Finished embroidered tiger logo with stitch texture"
                        draggable="false"
                    />

                    <div class="ba-before-clip" id="baBeforeClip">
                        <img
                            class="ba-img ba-img-before"
                            src="{{ asset('assets/images/ba-before.png') }}"
                            alt="Original flat tiger artwork before embroidery"
                            draggable="false"
                        />
                    </div>

                    <div class="ba-label ba-label-before">Before</div>
                    <div class="ba-label ba-label-after">After</div>

                    <div class="ba-handle" id="baHandle" role="slider" aria-label="Drag to compare before and after" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50" tabindex="0">
                        <span class="ba-handle-line" aria-hidden="true"></span>
                        <span class="ba-handle-grip" aria-hidden="true">
                            <i class="bi bi-arrows-expand-vertical"></i>
                        </span>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ============================================================
     COMPARISON SECTION — why choose us vs alternatives
     ============================================================ -->
    <section id="compare" aria-labelledby="cmp-heading">
        <div class="cmp-container">

            <div class="cmp-header" data-cmp-reveal>
                <div class="cmp-eyebrow">
                    <span class="cmp-eyebrow-dot"></span>
                    Why Choose Us
                </div>
                <h2 class="cmp-heading" id="cmp-heading">
                    See How We <span class="cmp-accent">Stack Up</span>
                </h2>
                <p class="cmp-subhead">
                    Not all branding providers are equal. Here is how Threads Embroidery &amp; Print compares to the alternatives.
                </p>
            </div>

            @php
                $cmpCriteria = [
                    'In-House Production',
                    'Live Customizer',
                    'Free Artwork',
                    'Quality Guarantee',
                    'UK Delivery',
                    'Bulk Orders',
                ];

                $cmpRows = [
                    [
                        'featured' => true,
                        'name' => 'Threads Embroidery & Print',
                        'desc' => 'Full-service custom embroidery and print with live preview, in-house production and dedicated UK support.',
                        'logo' => asset('assets/images/logo.png'),
                        'scores' => [true, true, true, true, true, true],
                    ],
                    [
                        'name' => 'Generic Online Printers',
                        'icon' => 'bi-globe2',
                        'desc' => 'Offshore production with limited garment choice and no real-time customisation.',
                        'scores' => [false, false, false, true, true, true],
                    ],
                    [
                        'name' => 'Local Freelancers',
                        'icon' => 'bi-person-workspace',
                        'desc' => 'Individual operators with variable capacity, turnaround and quality consistency.',
                        'scores' => [true, false, false, false, false, false],
                    ],
                    [
                        'name' => 'DIY / Home Printing',
                        'icon' => 'bi-house-gear',
                        'desc' => 'Low-cost entry point but poor durability, limited fabrics and no professional finish.',
                        'scores' => [false, false, false, false, false, false],
                    ],
                    [
                        'name' => 'Blank Garments Only',
                        'icon' => 'bi-bag',
                        'desc' => 'Plain garments with no branding — you still need embroidery or print applied separately.',
                        'scores' => [false, false, false, false, true, true],
                    ],
                ];
            @endphp

            <div class="cmp-table-wrap" data-cmp-reveal>
                <div class="cmp-table" role="table" aria-label="Comparison of branding providers">

                    <div class="cmp-row cmp-row--head" role="row">
                        <div class="cmp-cell cmp-cell--brand" role="columnheader"></div>
                        @foreach($cmpCriteria as $label)
                            <div class="cmp-cell cmp-cell--criteria" role="columnheader">{{ $label }}</div>
                        @endforeach
                    </div>

                    @foreach($cmpRows as $row)
                        <div class="cmp-row {{ !empty($row['featured']) ? 'cmp-row--featured' : '' }}" role="row">
                            <div class="cmp-cell cmp-cell--brand" role="rowheader">
                                @if(!empty($row['featured']))
                                    <div class="cmp-brand-logo">
                                        <img src="{{ $row['logo'] }}" alt="Threads Embroidery &amp; Print logo" loading="lazy">
                                    </div>
                                @else
                                    <div class="cmp-brand-icon">
                                        <i class="bi {{ $row['icon'] }}"></i>
                                    </div>
                                @endif
                                <div class="cmp-brand-text">
                                    <strong>{{ $row['name'] }}</strong>
                                    <span>{{ $row['desc'] }}</span>
                                </div>
                            </div>
                            @foreach($row['scores'] as $score)
                                <div class="cmp-cell cmp-cell--score {{ $score ? 'cmp-cell--yes' : 'cmp-cell--no' }}" role="cell">
                                    @if($score)
                                        <i class="bi bi-check-lg" aria-label="Yes"></i>
                                    @else
                                        <i class="bi bi-x-lg" aria-label="No"></i>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>
            </div>

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

    <!-- ========================================================
       HERO SECTION (legacy — below catalogue)
  ======================================================== -->
    <section id="hero" class="legacy-hero">

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

                    <h2 class="hero-headline">
                        Your Brand, Stitched Into
                        <span class="headline-accent"> Every Thread.</span>
                    </h2>

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
                            <span class="stat-num">10–14</span>
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
                                <strong>10–14</strong>
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

    </section>
    <!-- END HERO -->





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
                        10–14 working day turnaround with free UK delivery
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
