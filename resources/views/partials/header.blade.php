@php
    $categoryIcons = [
        'chefswear-and-catering' => 'bi-cup-hot-fill',
        'cleaning' => 'bi-stars',
        'college-and-uni' => 'bi-mortarboard-fill',
        'corporatewear' => 'bi-briefcase-fill',
        'health-salon-and-beauty' => 'bi-heart-pulse-fill',
        'hospitality' => 'bi-building-fill',
        'lounge-and-sleepwear' => 'bi-moon-stars-fill',
        'outdoor-leisure' => 'bi-tree-fill',
        'performance-and-activewear' => 'bi-lightning-fill',
        'promotional-accessories' => 'bi-gift-fill',
        'safetywear-and-ppe' => 'bi-shield-fill-check',
        'schoolwear' => 'bi-pencil-fill',
        'security' => 'bi-eye-fill',
        'sports' => 'bi-trophy-fill',
        'workwear' => 'bi-hammer',
    ];
@endphp
<!-- ========================================================
       HEADER
  ======================================================== -->
    <header id="site-header">

        <!-- TOP BAR — Auto-scrolling ticker -->
        <div class="header-top-bar">
            <div class="ticker-wrap">
                <div class="ticker-track" id="tickerTrack">
                    <!-- Items duplicated in JS for seamless loop -->
                    <span class="ticker-item"><i class="bi bi-telephone-fill"></i> &nbsp;+44 (0) 1234 567890</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-envelope-fill"></i>
                        &nbsp;customerservice@threadsembroideryprint.co.uk</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-lightning-charge-fill"></i> &nbsp;48-Hour Express
                        Turnaround Available</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-brush-fill"></i> &nbsp;Free Logo Artwork Setup on Every
                        Order</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-truck"></i> &nbsp;Free UK Delivery on Orders Over
                        £150</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-patch-check-fill"></i> &nbsp;100% Quality Guarantee — No
                        Compromises</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-geo-alt-fill"></i> &nbsp;Proudly UK Based &amp; Family
                        Run</span>
                    <span class="ticker-sep">✦</span>
                    <span class="ticker-item"><i class="bi bi-people-fill"></i> &nbsp;Trusted by 5,000+ Businesses
                        Across the UK</span>
                    <span class="ticker-sep">✦</span>
                </div>
            </div>
        </div>

        <!-- MAIN NAV -->
        <nav id="main-nav" class="navbar navbar-expand-lg">
            <div class="nav-inner">

                <!-- LEFT: Logo -->
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Threads Embroidery &amp; Print" class="nav-logo-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                    <!-- Fallback text logo shown if image missing -->
                    <div class="logo-fallback" style="display:none;">
                        <div class="logo-fallback-icon">
                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <circle cx="16" cy="16" r="15" stroke="#ee6023" stroke-width="2" />
                                <path d="M9 16 Q16 7 23 16 Q16 25 9 16Z" fill="#ee6023" />
                                <circle cx="16" cy="16" r="3" fill="#fff" />
                            </svg>
                        </div>
                        <div class="logo-fallback-text">
                            <span class="lf-main">THREADS</span>
                            <span class="lf-sub">Embroidery &amp; Print</span>
                        </div>
                    </div>
                </a>

                <!-- Hamburger (mobile) -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                    aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>

                <!-- CENTER: Nav Links -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav nav-center-list align-items-lg-center">

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About Us</a>
                        </li>

                        <!-- Catalogue with Mega Menu -->
                        <li class="nav-item mega-parent {{ request()->routeIs('shop.*') ? 'active' : '' }}" id="catalogueItem">
                            <a class="nav-link mega-trigger {{ request()->routeIs('shop.*') ? 'active' : '' }}" href="#" id="catalogueTrigger">
                                Catalogue <i class="bi bi-chevron-down chevron-icon ms-1"></i>
                            </a>

                            <!-- ===== MEGA MENU ===== -->
                            <div class="mega-menu" id="megaMenu">
                                <div class="mega-menu-inner">

                                    <div class="mega-header">
                                        <div class="mega-header-left">
                                            <span class="mega-eyebrow">Browse by Category</span>
                                            <h3 class="mega-title">Our Full Catalogue</h3>
                                        </div>
                                        <div class="mega-header-right">
                                            @if(!empty($catalogCategories))
                                                <a href="{{ route('shop.category', $catalogCategories[0]['slug']) }}" class="mega-cta-btn">View Full Catalogue &nbsp;<i
                                                        class="bi bi-arrow-right"></i></a>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mega-divider"></div>

                                    <div class="mega-categories">
                                        @foreach(($catalogCategories ?? []) as $cat)
                                            <a href="{{ route('shop.category', $cat['slug']) }}" class="mega-cat-item">
                                                <div class="cat-icon-wrap"><i class="bi {{ $categoryIcons[$cat['slug']] ?? 'bi-tag-fill' }}"></i></div>
                                                <div class="cat-info">
                                                    <span class="cat-name">{{ $cat['name'] }}</span>
                                                    <span class="cat-sub">{{ $cat['count'] }} products</span>
                                                </div>
                                                <i class="bi bi-chevron-right cat-arrow"></i>
                                            </a>
                                        @endforeach
                                    </div><!-- end .mega-categories -->

                                </div><!-- end .mega-menu-inner -->
                            </div>
                            <!-- ===== END MEGA MENU ===== -->
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact Us</a>
                        </li>

                    </ul>

                    <!-- RIGHT: Search + Auth Button -->
                    <div class="nav-right-slot">
                        <!-- Search toggle icon -->
                        <button class="nav-search-btn" id="navSearchToggle" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('login') }}" class="btn nav-quote-btn">Login / Signup <i
                                class="bi bi-arrow-right ms-1"></i></a>
                    </div>
                </div>

            </div>
        </nav>

        <!-- SEARCH OVERLAY -->
        <div class="nav-search-overlay" id="navSearchOverlay">
            <div class="nav-search-inner">
                <div class="nav-search-box">
                    <i class="bi bi-search nav-search-icon"></i>
                    <input type="text" id="navSearchInput" class="nav-search-input" placeholder="Search products, categories…" autocomplete="off" />
                    <button class="nav-search-close" id="navSearchClose" aria-label="Close search">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <!-- Suggestions dropdown -->
                <div class="nav-search-suggestions" id="navSearchSuggestions"></div>
            </div>
        </div>
    </header>

    @if(!empty($searchSuggestions))
        <script>window.__SEARCH_DATA__ = @json($searchSuggestions);</script>
    @endif
    <!-- END HEADER -->
