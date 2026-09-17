@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar" style="margin-bottom:1rem">
  <div></div>
  <div style="display:flex;gap:.55rem;flex-wrap:wrap">
    <button type="button" class="ad-btn ghost" id="bulkDiscountOpen">
      <i class="bi bi-percent"></i> Bulk quantity discounts
      @if($bulkDiscounts->isNotEmpty())
        <span class="ad-pill quoted" style="margin-left:.25rem">{{ $bulkDiscounts->count() }}</span>
      @endif
    </button>
    <a href="{{ route('admin.products-shop.products.index') }}" class="ad-btn ghost">Back</a>
  </div>
</div>

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
    Bulk discounts apply to total quantity across all sizes/colours for this style.
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

<div class="ad-modal-scrim" id="bulkDiscountModal" aria-hidden="true">
  <div class="ad-modal" role="dialog" aria-modal="true" aria-labelledby="bulkDiscountTitle" style="width:min(720px,100%)">
    <div class="ad-modal-head">
      <div>
        <h3 id="bulkDiscountTitle">Bulk quantity discounts</h3>
        <p>Percentage off garment unit price. Total quantity across all sizes counts. Ranges must not overlap.</p>
      </div>
      <button type="button" class="ad-modal-close" id="bulkDiscountClose" aria-label="Close">&times;</button>
    </div>

    <form method="POST" action="{{ route('admin.products-shop.products.bulk-discounts', $product->style_code) }}" id="bulkDiscountForm">
      @csrf
      @method('PUT')

      <div class="ad-bulk-tiers-head">
        <span>From qty</span>
        <span>Up to qty</span>
        <span>Discount %</span>
        <span></span>
      </div>
      <div id="bulkDiscountRows">
        @forelse($bulkDiscounts as $i => $tier)
          <div class="ad-bulk-tier-row">
            <input class="ad-input" type="number" min="1" step="1" name="tiers[{{ $i }}][qty_from]" value="{{ $tier->qty_from }}" required placeholder="From">
            <input class="ad-input" type="number" min="1" step="1" name="tiers[{{ $i }}][qty_to]" value="{{ $tier->qty_to }}" required placeholder="Up to">
            <input class="ad-input" type="number" min="0" max="100" step="0.01" name="tiers[{{ $i }}][discount_percent]" value="{{ number_format((float) $tier->discount_percent, 2, '.', '') }}" required placeholder="%">
            <button type="button" class="ad-btn ghost sm js-bulk-remove" aria-label="Remove row"><i class="bi bi-trash"></i></button>
          </div>
        @empty
        @endforelse
      </div>

      <div style="margin-top:.85rem;display:flex;gap:.55rem;flex-wrap:wrap">
        <button type="button" class="ad-btn ghost sm" id="bulkDiscountAdd"><i class="bi bi-plus"></i> Add range</button>
      </div>

      <p class="ad-field-hint" style="margin-top:.85rem">Leave empty and save to clear all bulk discounts for this product.</p>

      <div class="ad-form-actions">
        <button type="button" class="ad-btn ghost" id="bulkDiscountCancel">Cancel</button>
        <button type="submit" class="ad-btn primary">Save discounts</button>
      </div>
    </form>
  </div>
</div>
@endsection

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
.ad-bulk-tiers-head,.ad-bulk-tier-row{
  display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:.55rem;align-items:center;
}
.ad-bulk-tiers-head{
  font-size:.72rem;letter-spacing:.06em;text-transform:uppercase;color:var(--ad-muted);
  font-weight:700;margin-bottom:.45rem;
}
.ad-bulk-tier-row{margin-bottom:.45rem}
</style>
@endpush

@push('scripts')
<script>
(function(){
  const modal = document.getElementById('bulkDiscountModal');
  const rows = document.getElementById('bulkDiscountRows');
  if (!modal || !rows) return;

  let rowIndex = rows.querySelectorAll('.ad-bulk-tier-row').length;

  const open = () => {
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  };
  const close = () => {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
  };

  const addRow = (from = '', to = '', percent = '') => {
    const i = rowIndex++;
    const el = document.createElement('div');
    el.className = 'ad-bulk-tier-row';
    el.innerHTML = `
      <input class="ad-input" type="number" min="1" step="1" name="tiers[${i}][qty_from]" value="${from}" required placeholder="From">
      <input class="ad-input" type="number" min="1" step="1" name="tiers[${i}][qty_to]" value="${to}" required placeholder="Up to">
      <input class="ad-input" type="number" min="0" max="100" step="0.01" name="tiers[${i}][discount_percent]" value="${percent}" required placeholder="%">
      <button type="button" class="ad-btn ghost sm js-bulk-remove" aria-label="Remove row"><i class="bi bi-trash"></i></button>`;
    rows.appendChild(el);
  };

  document.getElementById('bulkDiscountOpen')?.addEventListener('click', open);
  document.getElementById('bulkDiscountClose')?.addEventListener('click', close);
  document.getElementById('bulkDiscountCancel')?.addEventListener('click', close);
  modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('open')) close();
  });

  document.getElementById('bulkDiscountAdd')?.addEventListener('click', () => addRow());

  rows.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-bulk-remove');
    if (!btn) return;
    btn.closest('.ad-bulk-tier-row')?.remove();
  });

  @if($errors->has('tiers') || $errors->has('tiers.*'))
    open();
  @endif
})();
</script>
@endpush
