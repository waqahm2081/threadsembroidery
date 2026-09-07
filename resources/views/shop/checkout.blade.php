@extends('layouts.app')

@section('title', 'Checkout &mdash; Threads Embroidery & Print Ltd')

@section('body_class')
checkout-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/shop-theme.css') }}?v=1.1">
<link rel="stylesheet" href="{{ asset('assets/css/cart-checkout.css') }}?v=1.7">
@endpush

@section('content')

@php
    $billingSame = session()->hasOldInput()
        ? (bool) old('billing_same_as_shipping')
        : true;
@endphp

@include('partials.page-hero', [
    'heroId' => 'checkout-hero',
    'eyebrow' => 'Secure Payment',
    'title' => 'Secure',
    'accent' => 'Checkout',
    'subtitle' => 'Complete your details below to place your custom embroidery order.',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Cart', 'url' => route('shop.cart')],
        ['label' => 'Checkout'],
    ],
])

<main id="checkout-main">
    <div class="cc-container">

        @include('partials.auth-prompt')

        <form action="{{ route('shop.checkout.store') }}" method="POST" class="cc-checkout-form" id="checkoutForm" novalidate>
            @csrf

            <div class="cc-layout cc-checkout-layout">
                <div class="cc-form-col">
                    <div class="cc-form-section">
                        <h2 class="cc-form-section-title"><i class="bi bi-person"></i> Contact Information</h2>
                        <div class="cc-form-row">
                            <div class="cc-field">
                                <label for="name">Full Name <span class="cc-required">*</span></label>
                                <input type="text" id="name" name="name" class="cc-input" value="{{ old('name', $user->name ?? '') }}" required>
                                @error('name')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="cc-form-row cc-form-row-2">
                            <div class="cc-field">
                                <label for="email">Email Address <span class="cc-required">*</span></label>
                                <input type="email" id="email" name="email" class="cc-input" value="{{ old('email', $user->email ?? '') }}" required>
                                @error('email')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="cc-field">
                                <label for="phone">Phone Number <span class="cc-required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="cc-input" value="{{ old('phone') }}" required>
                                @error('phone')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="cc-form-row">
                            <div class="cc-field">
                                <label for="company_name">Company Name <span class="cc-optional">(optional)</span></label>
                                <input type="text" id="company_name" name="company_name" class="cc-input" value="{{ old('company_name', $user->company_name ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="cc-form-section">
                        <h2 class="cc-form-section-title"><i class="bi bi-geo-alt"></i> Shipping Address</h2>
                        <div class="cc-form-row">
                            <div class="cc-field">
                                <label for="shipping_address_line1">Address Line 1 <span class="cc-required">*</span></label>
                                <input type="text" id="shipping_address_line1" name="shipping_address_line1" class="cc-input" value="{{ old('shipping_address_line1') }}" required>
                                @error('shipping_address_line1')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="cc-form-row cc-form-row-3">
                            <div class="cc-field">
                                <label for="shipping_city">City <span class="cc-required">*</span></label>
                                <input type="text" id="shipping_city" name="shipping_city" class="cc-input" value="{{ old('shipping_city') }}" required>
                                @error('shipping_city')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="cc-field">
                                <label for="shipping_state">State / County <span class="cc-required">*</span></label>
                                <input type="text" id="shipping_state" name="shipping_state" class="cc-input" value="{{ old('shipping_state') }}" required>
                                @error('shipping_state')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="cc-field">
                                <label for="shipping_zip">Zip / Postcode <span class="cc-required">*</span></label>
                                <input type="text" id="shipping_zip" name="shipping_zip" class="cc-input" value="{{ old('shipping_zip') }}" required>
                                @error('shipping_zip')<span class="cc-field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="cc-form-section">
                        <label class="cc-checkbox-item">
                            <input type="checkbox" name="billing_same_as_shipping" id="billingSame" value="1" {{ $billingSame ? 'checked' : '' }}>
                            <span>Billing address is the same as shipping address</span>
                        </label>

                        <div id="billingFields" style="{{ $billingSame ? 'display:none' : '' }}">
                            <h2 class="cc-form-section-title"><i class="bi bi-credit-card"></i> Billing Address</h2>
                            <div class="cc-form-row">
                                <div class="cc-field">
                                    <label for="billing_address_line1">Address Line 1</label>
                                    <input type="text" id="billing_address_line1" name="billing_address_line1" class="cc-input" value="{{ old('billing_address_line1') }}">
                                    @error('billing_address_line1')<span class="cc-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="cc-form-row cc-form-row-3">
                                <div class="cc-field">
                                    <label for="billing_city">City</label>
                                    <input type="text" id="billing_city" name="billing_city" class="cc-input" value="{{ old('billing_city') }}">
                                </div>
                                <div class="cc-field">
                                    <label for="billing_state">State / County</label>
                                    <input type="text" id="billing_state" name="billing_state" class="cc-input" value="{{ old('billing_state') }}">
                                </div>
                                <div class="cc-field">
                                    <label for="billing_zip">Zip / Postcode</label>
                                    <input type="text" id="billing_zip" name="billing_zip" class="cc-input" value="{{ old('billing_zip') }}">
                                </div>
                            </div>
                            <div class="cc-form-row">
                                <div class="cc-field">
                                    <label for="billing_company_name">Billing Company Name <span class="cc-optional">(optional)</span></label>
                                    <input type="text" id="billing_company_name" name="billing_company_name" class="cc-input" value="{{ old('billing_company_name') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cc-form-section">
                        <h2 class="cc-form-section-title"><i class="bi bi-credit-card-2-front"></i> Payment</h2>
                        <p class="cc-payment-help">Pay securely by card on this page. Your card details are handled by Stripe and never touch our servers.</p>
                        <div id="stripeCardElement" class="cc-stripe-element"></div>
                        <p id="stripeCardErrors" class="cc-stripe-error" role="alert"></p>
                        <input type="hidden" name="payment_intent_id" id="paymentIntentId" value="">
                    </div>

                    <div class="cc-form-section">
                        <h2 class="cc-form-section-title"><i class="bi bi-chat-left-text"></i> Additional Instructions</h2>
                        <div class="cc-form-row">
                            <div class="cc-field">
                                <textarea id="additional_instructions" name="additional_instructions" class="cc-textarea" rows="4" placeholder="Any special instructions for your order&hellip;">{{ old('additional_instructions') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="cc-summary-col">
                    <div class="cc-summary-card">
                        <h2 class="cc-summary-title">Your Order</h2>
                        <div class="cc-checkout-items">
                            @foreach($items as $item)
                                <div class="cc-checkout-item">
                                    <div class="cc-checkout-item-info">
                                        <strong>{{ $item->product_title }}</strong>
                                        <span>
                                            {{ $item->colour_name ?? '' }}
                                            @if($item->size_breakdown)
                                                &middot; @foreach($item->size_breakdown as $row){{ $row['size'] }} &times; {{ $row['qty'] }}{{ !$loop->last ? ', ' : '' }}@endforeach
                                            @elseif($item->size)
                                                &middot; {{ $item->size }}
                                            @endif
                                            &middot; Qty {{ $item->quantity }}
                                        </span>
                                        @if($item->hasLogoSetup())
                                            <span class="cc-checkout-addon"><i class="bi bi-plus-circle"></i> Embroidery Logo Set Up (+&pound;{{ number_format($item->logo_setup_fee, 2) }})</span>
                                        @endif
                                        @if($item->placementLabel())
                                            <span class="cc-checkout-addon"><i class="bi bi-bounding-box"></i> {{ $item->placementLabel() }}@if($item->decoration_fee > 0) (+&pound;{{ number_format($item->decoration_fee, 2) }}/unit)@endif</span>
                                        @endif
                                    </div>
                                    <span class="cc-checkout-item-price">&pound;{{ number_format($item->lineTotal(), 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="cc-summary-divider"></div>
                        <div class="cc-summary-row">
                            <span>Subtotal (ex. VAT)</span>
                            <strong>&pound;{{ number_format($subtotal, 2) }}</strong>
                        </div>
                        <div class="cc-summary-row">
                            <span>VAT ({{ \App\Services\ShopPricingService::vatPercent() }}%)</span>
                            <strong>&pound;{{ number_format($vat, 2) }}</strong>
                        </div>
                        <div class="cc-summary-row cc-summary-total">
                            <span>Total (inc. VAT)</span>
                            <strong>&pound;{{ number_format($total, 2) }}</strong>
                        </div>
                        <button type="submit" class="cc-btn-primary cc-btn-full" id="placeOrderBtn">
                            Pay &amp; Place Order <i class="bi bi-lock-fill"></i>
                        </button>
                        <p class="cc-secure-note"><i class="bi bi-shield-check"></i> Secure card payment via Stripe</p>
                        <a href="{{ route('shop.cart') }}" class="cc-btn-secondary cc-btn-full">Back to Cart</a>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</main>

@endsection

@push('scripts')
<script>
window.SHOP_ROUTES = {
  cart_destroy_base: @json(url('/shop/cart/items')),
  cart_url: @json(route('shop.cart')),
  checkout_url: @json(route('shop.checkout')),
};
</script>
<script src="{{ asset('js/stripe-checkout.js') }}?v=1.0"></script>
<script src="{{ asset('assets/js/cart-checkout.js') }}?v=1.9"></script>
@endpush
