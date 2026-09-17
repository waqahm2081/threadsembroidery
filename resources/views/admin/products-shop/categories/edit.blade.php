@extends('admin.layouts.app')

@section('content')
<div class="ad-card ad-school-form">
  <form method="POST" action="{{ route('admin.products-shop.categories.update', $category) }}">
    @csrf
    @method('PUT')

    <div class="ad-form-grid ad-form-grid--school">
      <div class="ad-fg">
        <label class="ad-label">Category name</label>
        <input class="ad-input" value="{{ $category->name }}" disabled>
        <p class="ad-field-hint">Name comes from PenCarrie catalogue paths and cannot be edited here.</p>
      </div>
      <div class="ad-fg">
        <label class="ad-label">Slug</label>
        <input class="ad-input" value="{{ $category->slug }}" disabled>
      </div>
      <div class="ad-fg">
        <label class="ad-label">Products in category</label>
        <input class="ad-input" value="{{ number_format($productCount) }}" disabled>
      </div>
      <div class="ad-fg ad-fg--check">
        <label class="ad-label">Shop link</label>
        <a class="ad-btn ghost sm" href="{{ route('shop.category', $category->slug) }}" target="_blank" rel="noopener">
          <i class="bi bi-box-arrow-up-right"></i> Open category
        </a>
      </div>

      <div class="ad-fg full ad-vat-box">
        <div class="ad-vat-box-grid">
          <div>
            <label class="ad-check-row ad-check-row--lg">
              <input type="hidden" name="vat_enabled" value="0">
              <input type="checkbox" name="vat_enabled" id="shopCatVatEnabled" value="1" {{ old('vat_enabled', $category->vat_enabled) ? 'checked' : '' }}>
              <span>Charge VAT for this category</span>
            </label>
            <p class="ad-field-hint">If any cart item is from a VAT-enabled category, VAT is applied to the whole cart using the highest enabled rate among those items.</p>
          </div>
          <div id="shopCatVatPercentWrap" style="{{ old('vat_enabled', $category->vat_enabled) ? '' : 'opacity:.5;pointer-events:none' }}">
            <label class="ad-label">VAT percentage (%)</label>
            <input class="ad-input @error('vat_percent') is-invalid @enderror" name="vat_percent" type="number" min="0" max="100" step="0.01" value="{{ old('vat_percent', $category->vat_percent ?? 20) }}">
            @error('vat_percent')<p class="ad-error">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="ad-form-actions">
      <a href="{{ route('admin.products-shop.categories.index') }}" class="ad-btn ghost">Back</a>
      <button class="ad-btn primary" type="submit">Save VAT settings</button>
    </div>
  </form>
</div>

@push('scripts')
<script>
(function(){
  const toggle = document.getElementById('shopCatVatEnabled');
  const wrap = document.getElementById('shopCatVatPercentWrap');
  if (!toggle || !wrap) return;
  const sync = () => {
    wrap.style.opacity = toggle.checked ? '' : '.5';
    wrap.style.pointerEvents = toggle.checked ? '' : 'none';
  };
  toggle.addEventListener('change', sync);
  sync();
})();
</script>
@endpush
@endsection
