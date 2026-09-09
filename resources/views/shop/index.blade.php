@extends('layouts.app')

@section('title', 'Shop — Threads Embroidery & Print Ltd')

@section('body_class')
shop-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.2">
<link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}?v=1.4">
@endpush

@section('content')

<section id="shop-hero">
    <div class="sh-container">
        <div class="sh-eyebrow">
            <span class="sh-eyebrow-dot"></span>
            Browse by Category
        </div>
        <h1 class="sh-title">Product <span class="sh-accent">Shop</span></h1>
        <p class="sh-sub">Explore our full catalogue — choose a category to browse customisable garments ready for embroidery or print.</p>
        <nav class="sh-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span class="sh-sep"><i class="bi bi-chevron-right"></i></span>
            <span>Shop</span>
        </nav>
    </div>
</section>

<section id="shop-index">
    <div class="si-container">
        <div class="si-intro">
            <div class="si-intro-copy">
                <h2 class="si-heading">Shop by category</h2>
                <p class="si-lede">{{ count($categories) }} ranges across workwear, schoolwear, sports and corporate — pick a category to start branding.</p>
            </div>
            <div class="si-intro-actions">
                <div class="si-stat">
                    <strong>{{ count($categories) }}</strong>
                    <span>Categories</span>
                </div>
                <a class="si-cart-btn" href="{{ route('shop.cart') }}"><i class="bi bi-bag"></i> Shop Cart</a>
            </div>
        </div>

        <div class="si-grid">
            @foreach($categories as $category)
                @php
                    $count = (int) ($category['count'] ?? 0);
                    $image = $category['image'] ?? null;
                    $icon = $category['icon'] ?? 'bi-grid';
                @endphp
                <a class="si-card{{ $image ? '' : ' si-card--empty' }}" href="{{ route('shop.category', $category['slug']) }}">
                    <span class="si-card-media">
                        @if($image)
                            <img src="{{ $image }}" alt="" loading="lazy" decoding="async">
                        @else
                            <span class="si-card-fallback"><i class="bi {{ $icon }}"></i></span>
                        @endif
                        <span class="si-card-badge">{{ $count }} {{ $count === 1 ? 'style' : 'styles' }}</span>
                    </span>
                    <span class="si-card-body">
                        <span class="si-card-icon" aria-hidden="true"><i class="bi {{ $icon }}"></i></span>
                        <span class="si-card-text">
                            <span class="si-card-name">{{ $category['name'] }}</span>
                            <span class="si-card-cta">Browse range <i class="bi bi-arrow-right"></i></span>
                        </span>
                    </span>
                </a>
            @endforeach
        </div>

        <div class="si-help">
            <div class="si-help-copy">
                <h3>Need something specific?</h3>
                <p>Can't find the garment you're after? Tell us what you need and we'll source it for you.</p>
            </div>
            <a class="si-help-btn" href="{{ route('contact') }}">Contact us <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>

@endsection
