@extends('layouts.app')

@section('title', 'Shop — Threads Embroidery & Print Ltd')

@section('body_class')
shop-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.1">
<link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}?v=1.2">
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
        <div class="si-toolbar">
            <p class="si-count">{{ count($categories) }} categor{{ count($categories) === 1 ? 'y' : 'ies' }}</p>
            <a class="si-cart-btn" href="{{ route('shop.cart') }}"><i class="bi bi-bag"></i> Shop Cart</a>
        </div>

        <div class="si-grid">
            @foreach($categories as $category)
                <a class="si-card" href="{{ route('shop.category', $category['slug']) }}">
                    <span class="si-card-name">{{ $category['name'] }}</span>
                    <span class="si-card-count">{{ (int) ($category['count'] ?? 0) }}</span>
                    <span class="si-card-arrow" aria-hidden="true"><i class="bi bi-arrow-right"></i></span>
                </a>
            @endforeach
        </div>

        <p class="si-foot">Can't find what you're looking for? <a href="{{ route('contact') }}">Contact us</a> and we'll source it for you.</p>
    </div>
</section>

@endsection
