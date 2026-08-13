@extends('admin.layouts.app')

@section('content')
<div class="ad-grid-2">
  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Preview</h2>
      <span class="ad-pill processing">{{ $quote->methodLabel() }}</span>
    </div>

    <div class="ad-quote-stage">
      @if($quote->product_image)
        <img class="ad-quote-garment" src="{{ $quote->product_image }}" alt="{{ $quote->product_title }}">
      @elseif($quote->preview_url)
        <img class="ad-quote-garment" src="{{ $quote->preview_url }}" alt="{{ $quote->product_title }}">
      @endif

      @foreach($marks as $mark)
        <div class="ad-quote-mark" style="left:{{ $mark['x'] }}%;top:{{ $mark['y'] }}%;width:{{ $mark['w'] }}%">
          <div class="ad-quote-mark-in">
            @if($quote->logo_url)
              <img src="{{ $quote->logo_url }}" alt="Logo">
            @else
              <span class="ad-quote-mph">LOGO</span>
            @endif
          </div>
          <span class="ad-quote-mark-lbl">{{ $mark['label'] }}</span>
        </div>
      @endforeach
    </div>

    @if($quote->logo_url)
      <h3 class="ad-section-title">Uploaded logo</h3>
      <div class="ad-quote-logo">
        <img src="{{ $quote->logo_url }}" alt="{{ $quote->logo_name }}">
        <span class="muted">{{ $quote->logo_name }}</span>
      </div>
    @endif
  </section>

  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Quote details</h2>
      <span class="muted">{{ optional($quote->created_at)->format('d M Y H:i') }}</span>
    </div>
    <dl class="ad-dl">
      <div><dt>Reference</dt><dd>{{ $quote->reference }}</dd></div>
      <div><dt>Product</dt><dd>{{ $quote->product_title }}</dd></div>
      <div><dt>Brand / code</dt><dd>{{ $quote->product_brand }} · {{ $quote->product_style_code }}</dd></div>
      <div><dt>Colour</dt><dd>{{ $quote->colour_name ?: '—' }}</dd></div>
      <div><dt>Method</dt><dd>{{ $quote->methodLabel() }}</dd></div>
      <div><dt>View</dt><dd>{{ ucfirst($quote->view) }}</dd></div>
      <div><dt>Quantity</dt><dd>{{ $quote->quantity }}</dd></div>
      @if($quote->user)
        <div><dt>Saved by</dt><dd>{{ $quote->user->name }}</dd></div>
      @endif
    </dl>

    <h3 class="ad-section-title">Placements</h3>
    @forelse($quote->placements ?? [] as $placement)
      <div class="ad-meta-row">
        <span>{{ $placement['label'] ?? 'Placement' }} @if(!empty($placement['size']))<span class="muted">({{ $placement['size'] }})</span>@endif</span>
        <b>+£{{ number_format((float) ($placement['price'] ?? 0), 2) }}/ea</b>
      </div>
    @empty
      <p class="muted">No placements.</p>
    @endforelse

    @if(!empty($quote->texts))
      <h3 class="ad-section-title">Personalisation</h3>
      @foreach($quote->texts as $text)
        @if(!empty($text['text']))
          <div class="ad-meta-row">
            <span>"{{ $text['text'] }}"</span>
            <b>{{ str_replace('_', ' ', $text['position'] ?? '') }}</b>
          </div>
        @endif
      @endforeach
    @endif

    <h3 class="ad-section-title">Sizes</h3>
    <div class="ad-chips">
      @forelse($quote->size_breakdown ?? [] as $size => $qty)
        <span class="ad-chip">{{ is_string($size) ? $size : ($qty['size'] ?? '') }} × {{ is_array($qty) ? ($qty['qty'] ?? 0) : $qty }}</span>
      @empty
        <span class="muted">—</span>
      @endforelse
    </div>

    <h3 class="ad-section-title">Pricing</h3>
    <dl class="ad-dl">
      <div><dt>Garment / ea</dt><dd>£{{ number_format($quote->garment_unit, 2) }}</dd></div>
      <div><dt>Branding / ea</dt><dd>£{{ number_format($quote->branding_unit, 2) }}</dd></div>
      <div><dt>Personalisation / ea</dt><dd>£{{ number_format($quote->text_unit, 2) }}</dd></div>
      <div><dt>Setup</dt><dd>£{{ number_format($quote->setup_fee, 2) }}</dd></div>
      <div><dt>Unit</dt><dd>£{{ number_format($quote->unit_price, 2) }}</dd></div>
      <div><dt>Total (ex VAT)</dt><dd><b>£{{ number_format($quote->subtotal, 2) }}</b></dd></div>
    </dl>
  </section>
</div>

<div class="ad-form-actions" style="justify-content:space-between">
  <a class="ad-btn ghost" href="{{ route('admin.product-quotes.index') }}"><i class="bi bi-arrow-left"></i> Back to quotes</a>
  <div class="ad-actions">
    <a class="ad-btn ghost" href="{{ route('admin.customizer') }}">New quote</a>
    <form method="POST" action="{{ route('admin.product-quotes.destroy', $quote) }}" onsubmit="return confirm('Delete this quote?')">
      @csrf
      @method('DELETE')
      <button class="ad-btn danger" type="submit">Delete</button>
    </form>
  </div>
</div>
@endsection
