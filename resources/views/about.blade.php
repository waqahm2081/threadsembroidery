@extends('layouts.app')

@section('title', 'About Us — Threads Embroidery &amp; Print Ltd')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/about.css') }}">
@endpush

@section('content')

<!-- ============================================================
         PAGE HERO / BREADCRUMB BANNER
    ============================================================ -->
    <section id="about-hero">
        <div class="abh-container">
            <div class="abh-eyebrow">
                <span class="abh-eyebrow-dot"></span>
                Who We Are
            </div>
            <h1 class="abh-title">About <span class="abh-accent">Threads</span></h1>
            <p class="abh-sub">UK's trusted custom embroidery &amp; print specialists — built on quality, driven by passion.</p>
            <nav class="abh-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span class="abh-sep"><i class="bi bi-chevron-right"></i></span>
                <span>About Us</span>
            </nav>
        </div>
    </section>


    <!-- ============================================================
         STORY SECTION — two-column with images
    ============================================================ -->
    <section id="about-story">
        <div class="abs-container">

            <!-- LEFT: Images -->
            <div class="abs-images">
                <div class="abs-img-main">
                    <img src="{{ asset('assets/images/aboutmain.jpg') }}" alt="Threads Embroidery team at work" loading="lazy" />
                    <div class="abs-years-badge">
                        <span class="abs-yb-num">15+</span>
                        <span class="abs-yb-label">Years of<br>Excellence</span>
                    </div>
                </div>
                <div class="abs-img-secondary">
                    <img src="{{ asset('assets/images/about-2.jpeg') }}" alt="Custom embroidery close-up" loading="lazy" />
                </div>
                <div class="abs-dot-grid"></div>
                <div class="abs-ring"></div>
            </div>

            <!-- RIGHT: Content -->
            <div class="abs-content">
                <div class="abs-eyebrow">
                    <span class="abs-eyebrow-dot"></span>
                    Our Story
                </div>
                <h2 class="abs-headline">
                    Built on Trust.<br>
                    <span class="abs-accent">Defined by Quality.</span>
                </h2>
                <p class="abs-lead">
                    At Threads Embroidery &amp; Print, we are more than just a uniform supplier — we are a dedicated branding partner for businesses, schools, and sports teams right across the United Kingdom. Our mission is straightforward: deliver exceptional custom embroidery and printing services that represent your brand with pride, every single time.
                </p>
                <p class="abs-lead">
                    Based in Cullompton, Devon, our team combines decades of hands-on experience with the very latest embroidery and printing technology. From initial artwork consultation right through to final delivery, we manage every step in-house — giving you faster turnarounds, better quality control, and a seamless experience from enquiry to completion.
                </p>

                <div class="abs-pillars">
                    
                    <div class="abs-pillar">
                        <div class="abs-pillar-icon"><i class="bi bi-people-fill"></i></div>
                        <div class="abs-pillar-text">
                            <strong>Client-First Approach</strong>
                            <span>Dedicated support and a tailored service every time.</span>
                        </div>
                    </div>
                    <div class="abs-pillar">
                        <div class="abs-pillar-icon"><i class="bi bi-calendar-check"></i></div>
                        <div class="abs-pillar-text">
                            <strong>Reliable Turnaround</strong>
                            <span>7–10 working days on orders with free UK delivery over £150.</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>


    @include('partials.branding-service')


    <!-- ============================================================
         STATS STRIP
    ============================================================ -->
    <section id="about-stats">
        <div class="abst-container">
            <div class="abst-item">
                <span class="abst-num">5,000+</span>
                <span class="abst-label">Happy Clients</span>
            </div>
            <div class="abst-divider"></div>
            <div class="abst-item">
                <span class="abst-num">15+</span>
                <span class="abst-label">Years Experience</span>
            </div>
            <div class="abst-divider"></div>
            <div class="abst-item">
                <span class="abst-num">7–10</span>
                <span class="abst-label">Working Day Turnaround</span>
            </div>
            <div class="abst-divider"></div>
            <div class="abst-item">
                <span class="abst-num">100%</span>
                <span class="abst-label">Quality Guaranteed</span>
            </div>
        </div>
    </section>


    <!-- ============================================================
         WHY CHOOSE US SECTION
    ============================================================ -->
    <section id="about-why">
        <div class="abw-container">

            <div class="abw-header">
                <div class="abw-eyebrow">
                    <span class="abw-eyebrow-dot"></span>
                    Why Choose Us
                </div>
                <h2 class="abw-heading">
                    The Threads <span class="abw-accent">Difference</span>
                </h2>
                <p class="abw-subhead">
                    We don't just print garments — we build lasting partnerships. Here's what sets us apart.
                </p>
            </div>

            <div class="abw-grid">

                <div class="abw-card">
                    <div class="abw-card-icon"><i class="bi bi-palette2"></i></div>
                    <h3 class="abw-card-title">Free Artwork Setup</h3>
                    <p class="abw-card-text">Our in-house design team handles all logo digitisation and artwork at no extra cost. You simply send your design — we do the rest.</p>
                </div>

                <div class="abw-card abw-card--featured">
                    <div class="abw-card-icon"><i class="bi bi-calendar-check"></i></div>
                    <h3 class="abw-card-title">7–10 Working Days</h3>
                    <p class="abw-card-text">Every order is completed within 7 to 10 working days from artwork approval — professional embroidery and print, delivered on time.</p>
                    <span class="abw-card-badge">Standard Turnaround</span>
                </div>

                <div class="abw-card">
                    <div class="abw-card-icon"><i class="bi bi-truck"></i></div>
                    <h3 class="abw-card-title">Free UK Delivery</h3>
                    <p class="abw-card-text">All orders over £150 qualify for free tracked delivery to anywhere in the United Kingdom. No hidden fees, ever.</p>
                </div>

                <div class="abw-card">
                    <div class="abw-card-icon"><i class="bi bi-patch-check-fill"></i></div>
                    <h3 class="abw-card-title">Quality Guarantee</h3>
                    <p class="abw-card-text">Every order is backed by our 100% quality guarantee. If anything isn't right, we'll fix it or replace it — no questions asked.</p>
                </div>

                <div class="abw-card">
                    <div class="abw-card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
                    <h3 class="abw-card-title">Huge Product Range</h3>
                    <p class="abw-card-text">From polo shirts and hoodies to hi-vis vests and chef wear — over 15 product categories, all fully customisable with your branding.</p>
                </div>

                <div class="abw-card">
                    <div class="abw-card-icon"><i class="bi bi-headset"></i></div>
                    <h3 class="abw-card-title">Dedicated Support</h3>
                    <p class="abw-card-text">A real person is always on hand to help — from choosing the right garment to managing bulk orders and ongoing accounts.</p>
                </div>

            </div>

            <div class="abw-cta">
                <a href="{{ route('contact') }}" class="btn abw-cta-btn">
                    Start Your Order Today
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>

        </div>
    </section>


    <!-- ============================================================
         TESTIMONIALS — shared include
    ============================================================ -->

@endsection

@section('after_content')
@include('partials.testimonials')
@endsection

@push('scripts')
<script src="{{ asset('assets/js/about.js') }}"></script>
@endpush
