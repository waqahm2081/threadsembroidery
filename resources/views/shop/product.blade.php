@php
    $activeColour = $product['colours'][0] ?? null;
    $mainImage = $activeColour['images'][0] ?? asset('assets/images/prod1.jpg');
    $displayMinPrice = \App\Services\ShopPricingService::displayUnitPrice($product['min_price']);
    $priceWhole = floor($displayMinPrice);
    $priceDecimal = str_pad((string) round(($displayMinPrice - $priceWhole) * 100), 2, '0', STR_PAD_LEFT);
@endphp

@extends('layouts.app')

@section('title', $product['title'] . ' — Threads Embroidery & Print Ltd')

@section('body_class')
product-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.1">
<link rel="stylesheet" href="{{ asset('assets/css/product-detail.css') }}?v=2.1">
@endpush

@section('content')

<section id="pd-breadcrumb">
    <div class="pd-breadcrumb-inner">
        <nav aria-label="Breadcrumb">
            <ol class="pd-breadcrumb-list">
                <li><a href="{{ route('home') }}"><i class="bi bi-house"></i> Home</a></li>
                <li class="pd-breadcrumb-sep"><i class="bi bi-chevron-right"></i></li>
                <li><a href="{{ route('shop.category', $category['slug']) }}">Shop</a></li>
                <li class="pd-breadcrumb-sep"><i class="bi bi-chevron-right"></i></li>
                <li><a href="{{ route('shop.category', $category['slug']) }}">{{ $category['name'] }}</a></li>
                <li class="pd-breadcrumb-sep"><i class="bi bi-chevron-right"></i></li>
                <li aria-current="page">{{ $product['title'] }}</li>
            </ol>
        </nav>
        <a href="javascript:history.back()" class="pd-breadcrumb-back">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</section>

<section id="pd-detail">
    <div class="pd-detail-grid">
        <div class="pd-images-col">
            <div class="pd-main-img-wrap">
                <img src="{{ $mainImage }}" alt="{{ $product['title'] }}" id="pdMainImage" class="pd-main-img">
                <div class="pd-zoom-hint"><i class="bi bi-zoom-in"></i></div>
                @if($product['is_new'])
                    <span class="pd-img-badge">New In</span>
                @elseif($product['is_clearance'])
                    <span class="pd-img-badge">Clearance</span>
                @endif
            </div>
            <div class="pd-thumbs" id="pdThumbs">
                @foreach(($activeColour['images'] ?? []) as $index => $image)
                    <button class="pd-thumb {{ $index === 0 ? 'active' : '' }}" data-src="{{ $image }}" aria-label="View image {{ $index + 1 }}">
                        <img src="{{ $image }}" alt="{{ $product['title'] }} image {{ $index + 1 }}">
                    </button>
                @endforeach
            </div>
        </div>

        <div class="pd-info-col">
            <div class="pd-info-top">
                <span class="pd-category-tag"><i class="bi bi-tag"></i> {{ $category['name'] }}</span>
                <span class="pd-rating">
                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                    <span class="pd-rating-val">4.9</span>
                    <span class="pd-rating-count">(127 reviews)</span>
                </span>
            </div>

            <h1 class="pd-product-name">{{ $product['title'] }}</h1>

            <div class="pd-price-row">
                <span class="pd-price-from">From</span>
                <span class="pd-price">£{{ $priceWhole }}<small>.{{ $priceDecimal }}</small></span>
                <span class="pd-price-vat">+ VAT</span>
                <span class="pd-price-unit">per unit</span>
                <span class="pd-price-note"><i class="bi bi-info-circle"></i> VAT calculated at checkout</span>
            </div>

            @if($product['brand'] || $product['type'])
                <p class="pd-short-desc">
                    @if($product['brand'])<strong>{{ $product['brand'] }}</strong>@endif
                    @if($product['brand'] && $product['type']) · @endif
                    @if($product['type']){{ $product['type'] }}@endif
                    @if($product['material']) · {{ $product['material'] }}@endif
                    @if($product['weight']) · {{ $product['weight'] }}@endif
                </p>
            @endif

            <div class="pd-divider"></div>

            <div class="pd-option-group">
                <div class="pd-option-label-row">
                    <span class="pd-option-label">Colour</span>
                    <span class="pd-selected-colour-name" id="pdColourName">{{ $activeColour['name'] ?? 'Select colour' }}</span>
                </div>
                <div class="pd-swatches" id="pdSwatches">
                    @foreach($product['colours'] as $index => $colour)
                        <button class="pd-swatch {{ $index === 0 ? 'active' : '' }}"
                                data-colour="{{ $colour['name'] }}"
                                data-code="{{ $colour['code'] }}"
                                data-images='@json($colour['images'])'
                                style="background:{{ $colour['hex'] }}; {{ in_array(strtolower($colour['hex']), ['#ffffff', '#fff', '#f0f0f0']) ? 'border-color:rgba(0,0,0,0.15);' : '' }}"
                                aria-label="{{ $colour['name'] }}"
                                title="{{ $colour['name'] }}"></button>
                    @endforeach
                </div>
            </div>

            <div class="pd-divider"></div>

            <div class="pd-option-group">
                <div class="pd-option-label-row">
                    <span class="pd-option-label">Sizes &amp; Quantities</span>
                    <a href="#" class="pd-size-guide-link"><i class="bi bi-rulers"></i> Size Guide</a>
                </div>
                <div class="pd-size-qty-grid" id="pdSizeQtyGrid">
                    <div class="pd-size-qty-header"><span>Size</span><span>Quantity</span></div>
                    @foreach(($activeColour['sizes'] ?? []) as $sizeRow)
                        @php $sizeSlug = \Illuminate\Support\Str::slug($sizeRow['size'], '-'); @endphp
                        <div class="pd-size-row-item" data-size="{{ $sizeRow['size'] }}">
                            <label class="pd-size-label" for="qty-{{ $sizeSlug }}"><span class="pd-size-chip">{{ $sizeRow['size'] }}</span></label>
                            <div class="pd-qty-ctrl">
                                <button type="button" class="pd-qty-btn pd-qty-minus" aria-label="Decrease {{ $sizeRow['size'] }}"><i class="bi bi-dash"></i></button>
                                <input type="number" id="qty-{{ $sizeSlug }}" class="pd-qty-input" value="0" min="0" max="999" data-unit-price="{{ $sizeRow['price_single'] }}">
                                <button type="button" class="pd-qty-btn pd-qty-plus" aria-label="Increase {{ $sizeRow['size'] }}"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pd-qty-summary">
                    <div class="pd-qty-summary-item">
                        <span>Total Units</span>
                        <strong id="pdTotalUnits">0</strong>
                    </div>
                    <div class="pd-qty-summary-divider"></div>
                    <div class="pd-qty-summary-item">
                        <span>Estimated Total <small class="pd-vat-note">(ex. VAT)</small></span>
                        <strong id="pdEstTotal" class="pd-est-total">£0.00</strong>
                    </div>
                </div>
            </div>

            <div class="pd-divider"></div>

            <div class="pd-option-group" id="pdAddonsGroup"
                 @if(empty($decoration['methods']) && empty($decoration['logo_setup_available'])) style="display:none" @endif>
                <div class="pd-option-label-row">
                    <span class="pd-option-label">Optional Add-ons</span>
                </div>
                @php
                    $hasPrint = in_array('print', $decoration['methods'] ?? [], true);
                    $hasEmbroidery = in_array('embroidery', $decoration['methods'] ?? [], true);
                    $defaultMethod = $hasPrint ? 'print' : ($hasEmbroidery ? 'embroidery' : null);
                @endphp
                <div class="pd-addons-list" id="pdAddonsList">
                    @if($hasPrint)
                    <label class="pd-addon-option" id="pdMethodPrintOption">
                        <input type="radio" id="pdMethodPrint" name="decoration_method" value="print"
                               {{ $defaultMethod === 'print' ? 'checked' : '' }}>
                        <span class="pd-addon-check pd-addon-check--radio"></span>
                        <span class="pd-addon-body">
                            <span class="pd-addon-title">Print</span>
                            <span class="pd-addon-desc">DTF / print decoration available for this product</span>
                        </span>
                    </label>
                    @endif

                    @if($hasEmbroidery)
                    <div class="pd-addon-stack" id="pdMethodEmbroideryStack">
                        <label class="pd-addon-option" id="pdMethodEmbroideryOption">
                            <input type="radio" id="pdMethodEmbroidery" name="decoration_method" value="embroidery"
                                   {{ $defaultMethod === 'embroidery' ? 'checked' : '' }}>
                            <span class="pd-addon-check pd-addon-check--radio"></span>
                            <span class="pd-addon-body">
                                <span class="pd-addon-title">Embroidery</span>
                                <span class="pd-addon-desc">Stitched embroidery decoration available for this product</span>
                            </span>
                        </label>

                        @if(!empty($decoration['logo_setup_available']))
                        <label class="pd-addon-option pd-addon-option--nested" id="pdLogoSetupOption"
                               @if($defaultMethod !== 'embroidery') hidden @endif>
                            <input type="checkbox" id="pdLogoSetup" name="logo_setup" value="1">
                            <span class="pd-addon-check"></span>
                            <span class="pd-addon-body">
                                <span class="pd-addon-title">Embroidery Logo Set Up</span>
                                <span class="pd-addon-desc">One-time digitisation fee for new embroidery artwork</span>
                            </span>
                            <span class="pd-addon-price">+£12.00 <small class="pd-vat-note">+ VAT</small></span>
                        </label>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="pd-divider"></div>

            <button type="button" class="pd-customize-btn" id="pdCustomizeBtn">
                <span class="pd-customize-btn-inner">
                    <i class="bi bi-magic"></i>
                    Upload Your Design / Customize
                </span>
                <span class="pd-customize-btn-glow"></span>
            </button>

            <button type="button" class="pd-customize-btn pd-enquire-btn is-hidden" id="pdEnquireBtn" hidden aria-hidden="true">
                <span class="pd-customize-btn-inner">
                    <i class="bi bi-envelope-paper"></i>
                    Enquire for Bulk Quote
                </span>
                <span class="pd-customize-btn-glow"></span>
            </button>
            <p class="pd-bulk-hint is-hidden" id="pdBulkHint" hidden>
                Quantities over 100 require a personal quote — submit an enquiry and we’ll get back to you.
            </p>

            <div class="pd-trust-row">
                <span class="pd-trust-item"><i class="bi bi-truck"></i> Free UK Delivery</span>
                <span class="pd-trust-item"><i class="bi bi-pencil-square"></i> Free Artwork</span>
                <span class="pd-trust-item"><i class="bi bi-calendar-check"></i> 10–14 Day Turnaround</span>
            </div>
        </div>
    </div>
</section>

<section id="pd-desc">
    <div class="pd-desc-container">
        <div class="pd-tabs">
            <button class="pd-tab active" data-tab="description">Description</button>
            <button class="pd-tab" data-tab="features">Product Features</button>
        </div>
        <div class="pd-tab-panel active" id="pd-panel-description">
            <div class="pd-desc-content">
                <h3 class="pd-desc-heading">About This Product</h3>
                @if($product['body'])
                    {!! $product['body'] !!}
                @else
                    <p>{{ $product['title'] }} from {{ $product['brand'] ?? 'our shop' }}.</p>
                @endif
            </div>
        </div>
        <div class="pd-tab-panel" id="pd-panel-features">
            <div class="pd-features-grid">
                <div class="pd-features-col">
                    <h3 class="pd-desc-heading">Product Specifications</h3>
                    <table class="pd-spec-table">
                        <tbody>
                            @if($product['brand'])<tr><td>Brand</td><td>{{ $product['brand'] }}</td></tr>@endif
                            @if($product['type'])<tr><td>Type</td><td>{{ $product['type'] }}</td></tr>@endif
                            @if($product['gender'])<tr><td>Gender</td><td>{{ $product['gender'] }}</td></tr>@endif
                            @if($product['material'])<tr><td>Material</td><td>{{ $product['material'] }}</td></tr>@endif
                            @if($product['weight'])<tr><td>Weight</td><td>{{ $product['weight'] }}</td></tr>@endif
                            <tr><td>Style Code</td><td>{{ $product['style_code'] }}</td></tr>
                            @foreach($product['attributes'] as $key => $value)
                                @if(is_string($key))
                                    <tr><td>{{ $key }}</td><td>{{ $value }}</td></tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pd-features-col">
                    <h3 class="pd-desc-heading">Available Options</h3>
                    <ul class="pd-feature-list">
                        <li>{{ count($product['colours']) }} colourways available</li>
                        @foreach($product['collections'] as $collection)
                            <li>{{ $collection }}</li>
                        @endforeach
                        @foreach($product['attributes'] as $key => $value)
                            @if(!is_string($key))
                                <li>{{ $value }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@if($related->isNotEmpty())
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
            @foreach($related as $item)
                @php
                    $relWhole = floor($item['min_price']);
                    $relDec = str_pad((string) round(($item['min_price'] - $relWhole) * 100), 2, '0', STR_PAD_LEFT);
                @endphp
                <article class="pd-related-card">
                    <div class="pd-related-img-wrap">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" loading="lazy">
                        <div class="pd-related-actions">
                            <a href="{{ $item['url'] }}" class="pd-related-quick-btn"><i class="bi bi-magic"></i> Customise</a>
                        </div>
                        @if($item['is_new'])<span class="pd-related-badge pd-related-badge--new">New In</span>@endif
                    </div>
                    <div class="pd-related-body">
                        <span class="pd-related-cat"><i class="bi bi-tag"></i> {{ $category['name'] }}</span>
                        <h3 class="pd-related-name">{{ $item['title'] }}</h3>
                        <div class="pd-related-footer">
                            <span class="pd-related-price">From £{{ $relWhole }}<small>.{{ $relDec }}</small> <small class="pd-vat-note">+ VAT</small></span>
                            <a href="{{ $item['url'] }}" class="pd-related-btn">Customise <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@include('shop.partials.reviews', ['productTitle' => $product['title']])

{{-- Bulk enquiry modal (qty > 100) --}}
<div class="pd-enq-modal" id="pdBulkEnquireModal" hidden aria-hidden="true">
    <div class="pd-enq-backdrop" id="pdEnqBackdrop"></div>
    <div class="pd-enq-dialog" role="dialog" aria-labelledby="pdBulkEnquireModalLabel" aria-modal="true">
        <div class="pd-enq-content">
            <div class="pd-enq-header">
                <div>
                    <h5 class="pd-enq-title" id="pdBulkEnquireModalLabel">
                        <i class="bi bi-envelope-paper"></i> Bulk Quote Enquiry
                    </h5>
                    <p class="pd-enq-sub">Orders over 100 units — tell us your details and we’ll prepare a quote.</p>
                </div>
                <button type="button" class="pd-enq-close" id="pdEnqClose" aria-label="Close">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form id="pdBulkEnquireForm" novalidate>
                <div class="pd-enq-body">
                    <div class="pd-enq-summary" id="pdEnquireSummary"></div>

                    <div class="pd-enq-error" id="pdEnquireError" hidden>
                        <i class="bi bi-exclamation-circle"></i>
                        <span id="pdEnquireErrorMsg"></span>
                    </div>

                    <div class="pd-enq-grid">
                        <div class="pd-enq-field">
                            <label for="pdEnqName">Full name <span>*</span></label>
                            <input type="text" id="pdEnqName" name="full_name" required maxlength="190" autocomplete="name">
                        </div>
                        <div class="pd-enq-field">
                            <label for="pdEnqEmail">Email <span>*</span></label>
                            <input type="email" id="pdEnqEmail" name="email" required maxlength="190" autocomplete="email">
                        </div>
                        <div class="pd-enq-field">
                            <label for="pdEnqPhone">Phone</label>
                            <input type="tel" id="pdEnqPhone" name="phone" maxlength="60" autocomplete="tel">
                        </div>
                        <div class="pd-enq-field">
                            <label for="pdEnqCompany">Company</label>
                            <input type="text" id="pdEnqCompany" name="company_name" maxlength="190" autocomplete="organization">
                        </div>
                        <div class="pd-enq-field pd-enq-field--full">
                            <label for="pdEnqMessage">Message / requirements</label>
                            <textarea id="pdEnqMessage" name="message" rows="4" maxlength="5000" placeholder="Artwork notes, deadline, branding preferences…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="pd-enq-footer">
                    <button type="button" class="pd-enq-cancel" id="pdEnqCancel">Cancel</button>
                    <button type="submit" class="pd-enq-submit" id="pdEnquireSubmit">
                        <i class="bi bi-send"></i> Submit Enquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
@php
    $productData = [
        'style_code' => $product['style_code'],
        'product_title' => $product['title'],
        'category_slug' => $category['slug'],
        'product_slug' => $product['slug'],
        'min_price' => $product['min_price'],
        'display_decoration_included' => $decoration['display_decoration_fee']
            ?? \App\Services\ShopPricingService::displayIncludedDecorationFee(),
        'colours' => $product['colours'],
        'pricing' => \App\Services\ShopPricingService::clientConfig(),
        'decoration' => $decoration,
        'customizer_url' => route('shop.product.customizer', [
            'categorySlug' => $category['slug'],
            'productSlug' => $product['slug'],
        ]),
    ];
@endphp
window.__PRODUCT_DATA__ = @json($productData);
window.SHOP_ROUTES = {
  cart_store: @json(route('shop.cart.store')),
  cart_url: @json(route('shop.cart')),
  checkout_url: @json(route('shop.checkout')),
  bulk_enquiry: @json(route('api.product-bulk-enquiries.store')),
};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/product-detail.js') }}?v=2.1"></script>
@endpush