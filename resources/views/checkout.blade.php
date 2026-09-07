@extends('layouts.app')

@section('content')
<main>
  @include('partials.guest-auth-bar')
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><a href="{{ route('cart') }}">Cart</a><i class="bi bi-chevron-right"></i><span>Checkout</span></div>
    <span class="h-eyebrow">Secure Checkout</span>
    <h1 class="mt-2">Complete Your Order</h1>
    <p class="lead">Enter your contact and delivery details — we’ll confirm your branded order by email.</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    <form id="checkoutForm" class="checkout-layout" novalidate>
      <div class="checkout-main" data-reveal>
        <div class="form-card checkout-card">
          <div class="checkout-section-head">
            <span class="n">1</span>
            <div><h3 class="h-3">Contact Details</h3><p class="muted" style="margin:.2rem 0 0;font-size:.9rem">We’ll use these to confirm your order.</p></div>
          </div>
          @php $profile = auth()->user(); @endphp
          <div class="field-group two">
            <div><label class="lbl">Full Name <span class="req">*</span></label><input class="input" name="name" required placeholder="Jane Smith" value="{{ old('name', $profile?->name) }}" autocomplete="name"><div class="err-msg">Please enter your name.</div></div>
            <div><label class="lbl">Email <span class="req">*</span></label><input class="input" type="email" name="email" required placeholder="you@company.co.uk" value="{{ old('email', $profile?->email) }}" autocomplete="email"><div class="err-msg">Enter a valid email.</div></div>
          </div>
          <div class="field-group two">
            <div><label class="lbl">Phone</label><input class="input" type="tel" name="phone" placeholder="07…" value="{{ old('phone', $profile?->phone) }}" autocomplete="tel"></div>
            <div><label class="lbl">Company / Organisation</label><input class="input" name="company_name" placeholder="Optional" value="{{ old('company_name', $profile?->company_name) }}" autocomplete="organization"></div>
          </div>
        </div>

        <div class="form-card checkout-card">
          <div class="checkout-section-head">
            <span class="n">2</span>
            <div><h3 class="h-3">Shipping Address</h3><p class="muted" style="margin:.2rem 0 0;font-size:.9rem">Where should we deliver your order?</p></div>
          </div>
          <div class="field-group"><label class="lbl">Address Line 1 <span class="req">*</span></label><input class="input" name="shipping_address_line1" required placeholder="Street address"><div class="err-msg">Enter your address.</div></div>
          <div class="field-group"><label class="lbl">Address Line 2</label><input class="input" name="shipping_address_line2" placeholder="Apartment, suite, etc."></div>
          <div class="field-group two">
            <div><label class="lbl">City / Town <span class="req">*</span></label><input class="input" name="shipping_city" required><div class="err-msg">Enter your city.</div></div>
            <div><label class="lbl">Postcode <span class="req">*</span></label><input class="input" name="shipping_postcode" required><div class="err-msg">Enter your postcode.</div></div>
          </div>
          <div class="field-group"><label class="lbl">Country</label><input class="input" name="shipping_country" value="United Kingdom"></div>
        </div>

        <div class="form-card checkout-card">
          <div class="checkout-section-head">
            <span class="n">3</span>
            <div><h3 class="h-3">Billing Address</h3><p class="muted" style="margin:.2rem 0 0;font-size:.9rem">For invoices and payment records.</p></div>
          </div>
          <label class="checkout-same">
            <input type="checkbox" id="billingSame" name="billing_same_as_shipping" value="1" checked>
            <span>Billing address is the same as shipping address</span>
          </label>
          <div id="billingFields" class="checkout-billing" hidden>
            <div class="field-group two">
              <div><label class="lbl">Billing Name <span class="req">*</span></label><input class="input" name="billing_name" placeholder="Name on invoice" value="{{ old('billing_name', $profile?->name) }}"><div class="err-msg">Enter billing name.</div></div>
              <div><label class="lbl">Billing Company</label><input class="input" name="billing_company_name" placeholder="Optional" value="{{ old('billing_company_name', $profile?->company_name) }}"></div>
            </div>
            <div class="field-group"><label class="lbl">Address Line 1 <span class="req">*</span></label><input class="input" name="billing_address_line1" placeholder="Street address"><div class="err-msg">Enter billing address.</div></div>
            <div class="field-group"><label class="lbl">Address Line 2</label><input class="input" name="billing_address_line2" placeholder="Apartment, suite, etc."></div>
            <div class="field-group two">
              <div><label class="lbl">City / Town <span class="req">*</span></label><input class="input" name="billing_city"><div class="err-msg">Enter billing city.</div></div>
              <div><label class="lbl">Postcode <span class="req">*</span></label><input class="input" name="billing_postcode"><div class="err-msg">Enter billing postcode.</div></div>
            </div>
            <div class="field-group"><label class="lbl">Country</label><input class="input" name="billing_country" value="United Kingdom"></div>
          </div>
        </div>

        <div class="form-card checkout-card">
          <div class="checkout-section-head">
            <span class="n">4</span>
            <div><h3 class="h-3">Payment</h3><p class="muted" style="margin:.2rem 0 0;font-size:.9rem">Pay securely by card — your details stay on this page.</p></div>
          </div>
          <div id="stripeCardElement" class="stripe-card-element"></div>
          <p id="stripeCardErrors" class="stripe-card-error" role="alert"></p>
          <p class="help mt-1"><i class="bi bi-shield-lock"></i> Payments are processed securely by Stripe. We never store your full card number.</p>
        </div>

        <div class="form-card checkout-card">
          <div class="checkout-section-head">
            <span class="n">5</span>
            <div><h3 class="h-3">Order Notes</h3><p class="muted" style="margin:.2rem 0 0;font-size:.9rem">Optional — anything we should know before production.</p></div>
          </div>
          <div class="field-group"><textarea class="textarea" name="notes" placeholder="Delivery instructions, artwork notes, deadlines…"></textarea></div>
        </div>
      </div>

      <aside class="checkout-side" data-reveal data-delay="1">
        <div class="card card--pad checkout-summary" id="checkoutSummary"></div>
      </aside>
    </form>
  </div></section>
</main>
@endsection
