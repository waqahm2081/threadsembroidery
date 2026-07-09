@extends('layouts.app')

@section('title', 'Order Confirmed — Threads Embroidery & Print Ltd')

@section('body_class')
checkout-page
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/cart-checkout.css') }}?v=1.3">
@endpush

@section('content')

@include('partials.page-hero', [
    'heroId' => 'checkout-hero',
    'eyebrow' => 'Order Complete',
    'title' => 'Thank',
    'accent' => 'You',
    'subtitle' => 'Your order has been received and is being processed.',
    'breadcrumbs' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Order Confirmed'],
    ],
])

<main id="checkout-main">
    <div class="cc-container">
        <div class="cc-confirmation">
            <div class="cc-confirmation-icon"><i class="bi bi-check-circle-fill"></i></div>
            <h2>Thank you for your order!</h2>
            <p>Your order <strong>#{{ $order->order_number }}</strong> has been received and is being processed.</p>
            <p class="cc-confirmation-email">A confirmation will be sent to <strong>{{ $order->email }}</strong>.</p>

            <div class="cc-confirmation-details">
                <h3>Order Details</h3>
                @foreach($order->items as $item)
                    <div class="cc-checkout-item">
                        <div class="cc-checkout-item-info">
                            <strong>{{ $item->product_title }}</strong>
                            <span>{{ $item->colour_name ?? '' }} · {{ $item->size ?? '—' }} · Qty {{ $item->quantity }}</span>
                        </div>
                        <span class="cc-checkout-item-price">£{{ number_format($item->line_total, 2) }}</span>
                    </div>
                @endforeach
                <div class="cc-summary-divider"></div>
                <div class="cc-summary-row">
                    <span>Subtotal (ex. VAT)</span>
                    <strong>£{{ number_format($order->subtotal, 2) }}</strong>
                </div>
                <div class="cc-summary-row">
                    <span>VAT (15%)</span>
                    <strong>£{{ number_format($order->vat_amount, 2) }}</strong>
                </div>
                <div class="cc-summary-row cc-summary-total">
                    <span>Total (inc. VAT)</span>
                    <strong>£{{ number_format($order->total, 2) }}</strong>
                </div>
            </div>

            <div class="cc-confirmation-actions">
                @auth
                    <a href="{{ route('user-dashboard') }}" class="cc-btn-primary">Go to Dashboard <i class="bi bi-arrow-right"></i></a>
                @else
                    <a href="{{ route('signup') }}" class="cc-btn-primary">Create Account to Track Order <i class="bi bi-arrow-right"></i></a>
                @endauth
                <a href="{{ route('shop') }}" class="cc-btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </div>
</main>

@endsection
