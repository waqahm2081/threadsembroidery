@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Order Confirmed</span></div>
    <span class="h-eyebrow">Thank you</span>
    <h1 class="mt-2">Order Received</h1>
    <p class="lead">Your order <b>{{ $order->order_number }}</b> is confirmed. A copy will be reviewed by our team shortly.</p>
  </div></section>

  <section class="section section--tight"><div class="container confirm-page">
    <div class="form-card checkout-card" data-reveal>
      <div class="confirm-status-row">
        <div>
          <div class="muted" style="font-size:.85rem;margin-bottom:.25rem">Order number</div>
          <b style="font-size:1.05rem">{{ $order->order_number }}</b>
        </div>
        <div>
          <div class="muted" style="font-size:.85rem;margin-bottom:.25rem">Status</div>
          <span class="pill pill--brand">{{ ucfirst($order->status) }}</span>
        </div>
        <div>
          <div class="muted" style="font-size:.85rem;margin-bottom:.25rem">Total</div>
          <b style="font-family:var(--font-display);font-size:1.35rem;color:var(--brand)">£{{ number_format($order->total, 2) }}</b>
        </div>
      </div>
    </div>

    <div class="form-card checkout-card" data-reveal>
      <h3 class="h-3" style="margin-bottom:1rem">Order Items</h3>
      @foreach ($order->items as $item)
        <div class="confirm-item">
          <div class="flex justify-between gap-2 wrap">
            <b>{{ $item->title }}</b>
            <b style="color:var(--brand)">£{{ number_format($item->line_total, 2) }}</b>
          </div>
          <div class="muted" style="font-size:.9rem;margin-top:.25rem">{{ $item->summary }}</div>
          <div class="muted" style="font-size:.85rem">Qty {{ $item->quantity }}@if($item->method) · {{ $item->method }}@endif</div>
          @if ($item->files->isNotEmpty())
            <div class="cart-art-btns" style="margin-top:.55rem">
              @foreach ($item->files as $file)
                <button type="button" class="cart-art-btn" data-artwork-modal
                  data-label="{{ $file->placement_label }}"
                  data-name="{{ $file->original_name }}"
                  data-url="{{ asset('storage/'.$file->path) }}">
                  <i class="bi bi-image"></i> {{ $file->placement_label }}
                </button>
              @endforeach
            </div>
          @endif
        </div>
      @endforeach

      <div class="confirm-totals">
        <div class="flex justify-between mt-1"><span class="muted">Subtotal</span><b>£{{ number_format($order->subtotal, 2) }}</b></div>
        <div class="flex justify-between mt-1"><span class="muted">VAT</span><b>£{{ number_format($order->vat_amount, 2) }}</b></div>
        <div class="flex justify-between mt-1"><span class="muted">Delivery</span><b>{{ $order->delivery_fee > 0 ? '£'.number_format($order->delivery_fee, 2) : 'FREE' }}</b></div>
        <div class="flex justify-between mt-2"><span style="font-weight:700">Total</span><b style="font-family:var(--font-display);font-size:1.4rem;color:var(--brand)">£{{ number_format($order->total, 2) }}</b></div>
      </div>
    </div>

    <div class="confirm-info-grid" data-reveal>
      <div class="form-card checkout-card">
        <div class="muted" style="font-size:.85rem;margin-bottom:.45rem">Contact</div>
        <div><b>{{ $order->name }}</b></div>
        <div class="muted">{{ $order->email }}</div>
        @if($order->phone)<div class="muted">{{ $order->phone }}</div>@endif
        @if($order->company_name)<div class="muted">{{ $order->company_name }}</div>@endif
      </div>
      <div class="form-card checkout-card">
        <div class="muted" style="font-size:.85rem;margin-bottom:.45rem">Shipping</div>
        <div>{{ $order->shipping_address_line1 }}</div>
        @if($order->shipping_address_line2)<div>{{ $order->shipping_address_line2 }}</div>@endif
        <div>{{ $order->shipping_city }}, {{ $order->shipping_postcode }}</div>
        <div class="muted">{{ $order->shipping_country }}</div>
      </div>
      <div class="form-card checkout-card">
        <div class="muted" style="font-size:.85rem;margin-bottom:.45rem">Billing</div>
        @if($order->billing_same_as_shipping)
          <div class="muted">Same as shipping address</div>
        @else
          <div><b>{{ $order->billing_name }}</b></div>
          @if($order->billing_company_name)<div class="muted">{{ $order->billing_company_name }}</div>@endif
          <div>{{ $order->billing_address_line1 }}</div>
          @if($order->billing_address_line2)<div>{{ $order->billing_address_line2 }}</div>@endif
          <div>{{ $order->billing_city }}, {{ $order->billing_postcode }}</div>
          <div class="muted">{{ $order->billing_country }}</div>
        @endif
      </div>
    </div>

    @if($order->notes)
      <div class="form-card checkout-card" data-reveal>
        <div class="muted" style="font-size:.85rem;margin-bottom:.45rem">Notes</div>
        <div>{{ $order->notes }}</div>
      </div>
    @endif

    <div class="confirm-actions" data-reveal>
      <a class="btn btn--lg" href="{{ route('bundles') }}">Browse More Bundles</a>
      <a class="btn btn--ghost btn--lg" href="{{ route('home') }}">Back Home</a>
    </div>
  </div></section>
</main>
@endsection
