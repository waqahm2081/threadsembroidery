@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET" action="{{ route('admin.products-shop.categories.index') }}">
    <input class="ad-input" type="search" name="q" value="{{ $q }}" placeholder="Search categories…">
    <button class="ad-btn ghost" type="submit"><i class="bi bi-search"></i> Filter</button>
  </form>
  <div class="ad-toolbar-actions" style="display:flex;gap:.55rem;flex-wrap:wrap">
    <button type="button" class="ad-btn ghost" id="vatBulkOpen">
      <i class="bi bi-sliders"></i> Edit Multiple VAT
    </button>
    <a class="ad-btn ghost" href="{{ route('admin.products-shop.products.index') }}"><i class="bi bi-bag"></i> Products</a>
  </div>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Category</th>
          <th>Slug</th>
          <th>Products</th>
          <th>VAT</th>
          <th>Rate</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $category)
          <tr>
            <td><b>{{ $category->name }}</b></td>
            <td><span class="muted">{{ $category->slug }}</span></td>
            <td>{{ number_format($counts[$category->slug] ?? 0) }}</td>
            <td>
              @if($category->vat_enabled)
                <span class="ad-pill quoted">Enabled</span>
              @else
                <span class="ad-pill">Off</span>
              @endif
            </td>
            <td>{{ $category->vat_enabled ? rtrim(rtrim(number_format($category->vat_percent, 2), '0'), '.').'%' : '—' }}</td>
            <td>
              <button
                type="button"
                class="ad-btn ghost sm js-vat-edit"
                data-name="{{ $category->name }}"
                data-enabled="{{ $category->vat_enabled ? '1' : '0' }}"
                data-percent="{{ rtrim(rtrim(number_format((float) $category->vat_percent, 2, '.', ''), '0'), '.') ?: '0' }}"
                data-action="{{ route('admin.products-shop.categories.update', $category) }}"
              >Edit VAT</button>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No categories found. Import PenCarrie products first.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($categories->hasPages())
    <div class="ad-pagination">{{ $categories->links('vendor.pagination.admin') }}</div>
  @endif
</section>

<div class="ad-modal-scrim" id="vatModal" aria-hidden="true">
  <div class="ad-modal" role="dialog" aria-modal="true" aria-labelledby="vatModalTitle">
    <div class="ad-modal-head">
      <div>
        <h3 id="vatModalTitle">Edit VAT</h3>
        <p id="vatModalSubtitle">Category VAT settings</p>
      </div>
      <button type="button" class="ad-modal-close" id="vatModalClose" aria-label="Close">&times;</button>
    </div>
    <form method="POST" id="vatModalForm">
      @csrf
      <input type="hidden" name="_method" id="vatModalMethod" value="PUT">
      <div class="ad-vat-box">
        <div class="ad-vat-box-grid">
          <div>
            <label class="ad-check-row ad-check-row--lg">
              <input type="hidden" name="vat_enabled" value="0">
              <input type="checkbox" name="vat_enabled" id="vatModalEnabled" value="1">
              <span id="vatModalEnabledLabel">Charge VAT for this category</span>
            </label>
            <p class="ad-field-hint" id="vatModalHint">If any cart item is from a VAT-enabled category, the highest enabled rate among those items is applied to the whole cart.</p>
          </div>
          <div id="vatModalPercentWrap">
            <label class="ad-label">VAT percentage (%)</label>
            <input class="ad-input" name="vat_percent" id="vatModalPercent" type="number" min="0" max="100" step="0.01" value="20">
          </div>
        </div>
      </div>
      <div class="ad-form-actions">
        <button type="button" class="ad-btn ghost" id="vatModalCancel">Cancel</button>
        <button type="submit" class="ad-btn primary" id="vatModalSubmit">Save VAT</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const modal = document.getElementById('vatModal');
  const form = document.getElementById('vatModalForm');
  const title = document.getElementById('vatModalTitle');
  const subtitle = document.getElementById('vatModalSubtitle');
  const method = document.getElementById('vatModalMethod');
  const enabled = document.getElementById('vatModalEnabled');
  const enabledLabel = document.getElementById('vatModalEnabledLabel');
  const hint = document.getElementById('vatModalHint');
  const percent = document.getElementById('vatModalPercent');
  const percentWrap = document.getElementById('vatModalPercentWrap');
  const submit = document.getElementById('vatModalSubmit');
  const bulkAction = @json(route('admin.products-shop.categories.update-all'));
  if (!modal || !form || !enabled || !percent || !percentWrap) return;

  const syncPercent = () => {
    percentWrap.style.opacity = enabled.checked ? '' : '.5';
    percentWrap.style.pointerEvents = enabled.checked ? '' : 'none';
  };

  const openSingle = (btn) => {
    form.action = btn.dataset.action;
    method.value = 'PUT';
    title.textContent = 'Edit VAT';
    subtitle.textContent = btn.dataset.name || 'Category VAT settings';
    enabledLabel.textContent = 'Charge VAT for this category';
    hint.textContent = 'If any cart item is from a VAT-enabled category, the highest enabled rate among those items is applied to the whole cart.';
    submit.textContent = 'Save VAT';
    enabled.checked = btn.dataset.enabled === '1';
    percent.value = btn.dataset.percent || '20';
    syncPercent();
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  };

  const openBulk = () => {
    form.action = bulkAction;
    method.value = 'PUT';
    title.textContent = 'Edit Multiple VAT';
    subtitle.textContent = 'These settings will be applied to every category.';
    enabledLabel.textContent = 'Charge VAT for all categories';
    hint.textContent = 'This overwrites VAT on/off and the percentage for all shop categories.';
    submit.textContent = 'Apply to all categories';
    enabled.checked = false;
    percent.value = '20';
    syncPercent();
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
  };

  const close = () => {
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
  };

  document.querySelectorAll('.js-vat-edit').forEach((btn) => {
    btn.addEventListener('click', () => openSingle(btn));
  });
  document.getElementById('vatBulkOpen')?.addEventListener('click', openBulk);
  document.getElementById('vatModalClose')?.addEventListener('click', close);
  document.getElementById('vatModalCancel')?.addEventListener('click', close);
  modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('open')) close(); });
  enabled.addEventListener('change', syncPercent);
  syncPercent();
})();
</script>
@endpush
