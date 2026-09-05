@extends('layouts.app')

@section('title', 'Shopping Cart &mdash; Threads Embroidery & Print Ltd')

@section('body_class')
cart-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.1">
<link rel="stylesheet" href="{{ asset('assets/css/cart-checkout.css') }}?v=1.8">
@endpush

@section('content')

@include('partials.page-hero', [
    'heroId' => 'cart-hero',
    'eyebrow' => 'Your Order',
    'title' => 'Your',
    'accent' => 'Cart',
    'subtitle' => 'Review your items before proceeding to checkout.',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Cart'],
    ],
])

<main id="cart-main">
    <div class="cc-container">

        @include('partials.auth-prompt')

        @if(session('error'))
            <div class="cc-alert cc-alert-error"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @if($items->isEmpty())
            <div class="cc-empty">
                <div class="cc-empty-icon"><i class="bi bi-cart-x"></i></div>
                <h2>Your cart is empty</h2>
                <p>Browse our shop and add products to get started.</p>
                <a href="{{ route('shop') }}" class="cc-btn-primary">Browse Shop <i class="bi bi-arrow-right"></i></a>
            </div>
        @else
            <div class="cc-layout">
                <div class="cc-items-col">
                    @foreach($items as $item)
                        <article class="cc-item" data-item-id="{{ $item->id }}">
                            <div class="cc-item-preview">
                                @if($item->product_image)
                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_title }}">
                                @else
                                    <div class="cc-item-placeholder">
                                        <i class="bi bi-box"></i>
                                        <span>Product</span>
                                    </div>
                                @endif
                            </div>
                            <div class="cc-item-details">
                                <h3 class="cc-item-title">{{ $item->product_title }}</h3>
                                <div class="cc-item-meta">
                                    @if($item->colour_name)
                                        <span><i class="bi bi-palette"></i> {{ $item->colour_name }}</span>
                                    @endif
                                    @if($item->size_breakdown)
                                        <span><i class="bi bi-rulers"></i>
                                            @foreach($item->size_breakdown as $row)
                                                {{ $row['size'] }} &times; {{ $row['qty'] }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </span>
                                    @elseif($item->size)
                                        <span><i class="bi bi-rulers"></i> Size: {{ $item->size }}</span>
                                    @endif
                                    <span><i class="bi bi-stack"></i> Qty: {{ $item->quantity }}</span>
                                </div>
                                @if($item->hasLogoSetup())
                                    <div class="cc-item-addon">
                                        <i class="bi bi-plus-circle"></i> Embroidery Logo Set Up (+&pound;{{ number_format($item->logo_setup_fee, 2) }})
                                    </div>
                                @endif
                                @if($item->placementLabel())
                                    <div class="cc-item-addon">
                                        <i class="bi bi-bounding-box"></i> {{ $item->placementLabel() }}
                                        @if($item->decoration_fee > 0)
                                            (+&pound;{{ number_format($item->decoration_fee, 2) }}/unit)
                                        @endif
                                    </div>
                                @endif
                                @if($item->size_breakdown && count($item->size_breakdown) > 1)
                                    <div class="cc-size-breakdown">
                                        @foreach($item->size_breakdown as $row)
                                            <span>{{ $row['size'] }} &times; {{ $row['qty'] }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="cc-item-actions-row">
                                    @if(!$item->is_customized)
                                        <a href="{{ $item->customizerUrl() }}" class="cc-customize-link">
                                            <i class="bi bi-magic"></i> Complete your design
                                        </a>
                                    @else
                                        <button type="button" class="cc-view-design-btn" data-design-id="{{ $item->id }}">
                                            <i class="bi bi-eye"></i> View Design
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="cc-item-actions">
                                <div class="cc-item-price">&pound;{{ number_format($item->lineTotal(), 2) }}</div>
                                <button type="button" class="cc-remove-btn" data-remove-item="{{ $item->id }}" aria-label="Remove item">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="cc-summary-col">
                    <div class="cc-summary-card">
                        <h2 class="cc-summary-title">Order Summary</h2>
                        <div class="cc-summary-row">
                            <span>Subtotal (ex. VAT)</span>
                            <strong id="cartSummarySubtotal">&pound;{{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="cc-summary-row">
                            <span>VAT ({{ \App\Services\ShopPricingService::vatPercent() }}%)</span>
                            <strong id="cartSummaryVat">&pound;{{ number_format($vat, 2) }}</strong>
                        </div>
                        <div class="cc-summary-row cc-summary-note">
                            <span><i class="bi bi-truck"></i> Delivery calculated at checkout</span>
                        </div>
                        <div class="cc-summary-divider"></div>
                        <div class="cc-summary-row cc-summary-total">
                            <span>Estimated Total (inc. VAT)</span>
                            <strong id="cartSummaryTotal">&pound;{{ number_format($total, 2) }}</strong>
                        </div>
                        <a href="{{ route('shop.checkout') }}" class="cc-btn-primary cc-btn-full">Proceed to Checkout <i class="bi bi-arrow-right"></i></a>
                        <a href="{{ route('shop') }}" class="cc-btn-secondary cc-btn-full">Continue Shopping</a>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</main>

{{-- Design preview modal (custom overlay — site does not load Bootstrap CSS) --}}
<div class="cdm-modal" id="cartDesignModal" hidden aria-hidden="true">
    <div class="cdm-modal-backdrop" id="cdmModalBackdrop"></div>
    <div class="cdm-modal-dialog" role="dialog" aria-labelledby="cartDesignModalLabel" aria-modal="true">
        <div class="cdm-content">
            <div class="cdm-header">
                <div>
                    <h5 class="cdm-title" id="cartDesignModalLabel"><i class="bi bi-palette"></i> Your Design</h5>
                    <p class="cdm-sub" id="cdmSubtitle"></p>
                </div>
                <button type="button" class="cdm-close" id="cdmModalClose" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="cdm-body">
                <div class="cdm-preview-wrap">
                    <div class="cdm-preview-canvas" id="cdmPreviewCanvas">
                        <img class="cdm-garment-img" id="cdmGarmentImg" src="" alt="Garment preview">
                        <div class="cdm-layers" id="cdmLayers"></div>
                    </div>
                    <div class="cdm-artwork-preview" id="cdmArtworkPreview" style="display:none"></div>
                </div>
                <div class="cdm-details" id="cdmDetails"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
window.__CART_DESIGNS__ = @json($designPreviews ?? []);
window.SHOP_ROUTES = {
  cart_destroy_base: @json(url('/shop/cart/items')),
  cart_url: @json(route('shop.cart')),
  checkout_url: @json(route('shop.checkout')),
};
</script>
<script src="{{ asset('assets/js/design-print-area.js') }}?v=1.2"></script>
<script src="{{ asset('assets/js/cart-checkout.js') }}?v=1.8"></script>
@endpush
