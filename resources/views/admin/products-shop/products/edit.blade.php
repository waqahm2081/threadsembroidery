@extends('admin.layouts.app')

@section('content')
<div class="ad-card ad-school-form">
  <div class="ad-form-grid ad-form-grid--school" style="margin-bottom:1.4rem">
    <div class="ad-fg">
      <label class="ad-label">Style code</label>
      <input class="ad-input" value="{{ $product->style_code }}" disabled>
    </div>
    <div class="ad-fg">
      <label class="ad-label">Brand</label>
      <input class="ad-input" value="{{ $product->brand ?: '—' }}" disabled>
    </div>
    <div class="ad-fg">
      <label class="ad-label">Type</label>
      <input class="ad-input" value="{{ $product->type ?: '—' }}" disabled>
    </div>
    <div class="ad-fg">
      <label class="ad-label">Gender</label>
      <input class="ad-input" value="{{ $product->gender ?: '—' }}" disabled>
    </div>
    <div class="ad-fg full">
      <label class="ad-label">Categories (PenCarrie)</label>
      <input class="ad-input" value="{{ $categories ? implode(', ', $categories) : '—' }}" disabled>
    </div>
  </div>

  <p class="ad-field-hint" style="margin-bottom:1.2rem">
    Images and colourways are supplied by PenCarrie and cannot be edited here. You can update prices for each size variation.
  </p>

  <form method="POST" action="{{ route('admin.products-shop.products.update', $product->style_code) }}">
    @csrf
    @method('PUT')

    @foreach($colours as $colour)
      <section class="ad-ps-colour">
        <div class="ad-ps-colour-head">
          <span class="ad-ps-swatch" style="background:{{ $colour['hex'] }}"></span>
          <div>
            <h3>{{ $colour['name'] }}</h3>
            <span class="muted">{{ $colour['code'] }} · {{ $colour['hex'] }}</span>
          </div>
        </div>

        @if(!empty($colour['images']))
          <div class="ad-ps-images">
            @foreach($colour['images'] as $img)
              <a href="{{ $img }}" target="_blank" rel="noopener" class="ad-ps-thumb">
                <img src="{{ $img }}" alt="{{ $colour['name'] }}">
              </a>
            @endforeach
          </div>
        @endif

        <div class="ad-table-wrap">
          <table class="ad-table ad-ps-price-table">
            <thead>
              <tr>
                <th>SKU</th>
                <th>Size</th>
                <th>Price (£)</th>
              </tr>
            </thead>
            <tbody>
              @foreach($colour['sizes'] as $row)
                <tr>
                  <td><code>{{ $row->sku }}</code></td>
                  <td><b>{{ $row->size }}</b></td>
                  <td style="max-width:160px">
                    <input class="ad-input" type="number" min="0" step="0.01"
                           name="prices[{{ $row->id }}][price_single]"
                           value="{{ old('prices.'.$row->id.'.price_single', number_format((float) $row->price_single, 2, '.', '')) }}"
                           required>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </section>
    @endforeach

    <div class="ad-form-actions">
      <a href="{{ route('admin.products-shop.products.index') }}" class="ad-btn ghost">Back</a>
      <button class="ad-btn primary" type="submit"><i class="bi bi-check2"></i> Save prices</button>
    </div>
  </form>
</div>

@push('styles')
<style>
.ad-ps-colour{
  border:1px solid var(--ad-line);
  border-radius:14px;
  padding:1.1rem 1.2rem;
  margin-bottom:1.2rem;
  background:var(--ad-bg-2);
}
.ad-ps-colour-head{display:flex;align-items:center;gap:.85rem;margin-bottom:.9rem}
.ad-ps-colour-head h3{margin:0;font-size:1rem}
.ad-ps-swatch{width:28px;height:28px;border-radius:999px;border:1px solid var(--ad-line);flex-shrink:0}
.ad-ps-images{display:flex;flex-wrap:wrap;gap:.55rem;margin-bottom:1rem}
.ad-ps-thumb{
  display:block;width:72px;height:72px;border-radius:10px;overflow:hidden;
  border:1px solid var(--ad-line);background:#fff;
}
.ad-ps-thumb img{width:100%;height:100%;object-fit:contain}
.ad-ps-price-table .ad-input{padding:.55rem .65rem;min-width:110px;max-width:160px}
</style>
@endpush
@endsection
