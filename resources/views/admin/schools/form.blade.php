@extends('admin.layouts.app')
@section('title', $editing ? 'Edit School' : 'New School')
@php $nav = 'schools'; @endphp

@section('content')
<div class="ad-card ad-school-form">
  <form method="POST" action="{{ $editing ? route('admin.schools.update', $school) : route('admin.schools.store') }}" enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="ad-form-grid ad-form-grid--school">
      <div class="ad-fg full">
        <label class="ad-label">School Name <span class="req">*</span></label>
        <input class="ad-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', $school->name) }}" required>
        @error('name')<p class="ad-error">{{ $message }}</p>@enderror
      </div>

      <div class="ad-fg">
        <label class="ad-label">Contact Email</label>
        <input class="ad-input" name="contact_email" type="email" value="{{ old('contact_email', $school->contact_email) }}">
      </div>

      <div class="ad-fg">
        <label class="ad-label">Contact Phone</label>
        <input class="ad-input" name="contact_phone" value="{{ old('contact_phone', $school->contact_phone) }}">
      </div>

      <div class="ad-fg full">
        <label class="ad-label">Address</label>
        <input class="ad-input" name="address" value="{{ old('address', $school->address) }}">
      </div>

      <div class="ad-fg full">
        <label class="ad-label">Description</label>
        <textarea class="ad-input" name="description" rows="4" style="resize:vertical">{{ old('description', $school->description) }}</textarea>
      </div>

      <div class="ad-fg">
        <label class="ad-label">Accent Colour</label>
        <div class="ad-inline-row">
          <input type="color" name="accent_colour" value="{{ old('accent_colour', $school->accent_colour ?: '#e85820') }}" class="ad-colour-swatch">
          <input class="ad-input" name="accent_colour_text" value="{{ old('accent_colour', $school->accent_colour ?: '#e85820') }}" style="font-family:monospace" placeholder="#e85820" oninput="this.previousElementSibling.value=this.value">
        </div>
      </div>

      <div class="ad-fg">
        <label class="ad-label">Sort Order</label>
        <input class="ad-input" name="sort_order" type="number" min="0" value="{{ old('sort_order', $school->sort_order ?? 0) }}">
      </div>

      <div class="ad-fg">
        <label class="ad-label">Store PIN <span style="font-weight:500;color:var(--ad-muted)">(4 digits)</span></label>
        <input class="ad-input @error('pin') is-invalid @enderror" name="pin" type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" value="{{ old('pin', $school->pin) }}" placeholder="Leave blank for 1111">
        <p class="ad-field-hint">Parents enter this PIN to unlock the school catalogue. Blank = <b>1111</b>.</p>
        @error('pin')<p class="ad-error">{{ $message }}</p>@enderror
      </div>

      <div class="ad-fg ad-fg--check">
        <label class="ad-label">Status</label>
        <label class="ad-check-row">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $school->is_active ?? true) ? 'checked' : '' }}>
          <span>Active school</span>
        </label>
      </div>

      <div class="ad-fg full ad-vat-box">
        <div class="ad-vat-box-grid">
          <div>
            <label class="ad-check-row ad-check-row--lg">
              <input type="hidden" name="vat_enabled" value="0">
              <input type="checkbox" name="vat_enabled" id="schoolVatEnabled" value="1" {{ old('vat_enabled', $school->vat_enabled ?? false) ? 'checked' : '' }}>
              <span>Charge VAT for this school</span>
            </label>
            <p class="ad-field-hint">Off by default — turn on only when VAT should be added at checkout.</p>
          </div>
          <div id="schoolVatPercentWrap" style="{{ old('vat_enabled', $school->vat_enabled ?? false) ? '' : 'opacity:.5;pointer-events:none' }}">
            <label class="ad-label">VAT percentage (%)</label>
            <input class="ad-input @error('vat_percent') is-invalid @enderror" name="vat_percent" type="number" min="0" max="100" step="0.01" value="{{ old('vat_percent', $school->vat_percent ?? 20) }}">
            @error('vat_percent')<p class="ad-error">{{ $message }}</p>@enderror
          </div>
        </div>
      </div>

      <div class="ad-fg full">
        <label class="ad-label">School Logo</label>
        @if($editing && $school->logo_url)
          <div class="ad-logo-preview">
            <img src="{{ $school->logo_url }}" alt="">
            <label class="ad-check-row">
              <input type="checkbox" name="remove_logo" value="1">
              <span>Remove logo</span>
            </label>
          </div>
        @endif
        <input class="ad-input" type="file" name="logo" accept="image/*">
      </div>
    </div>

    <div class="ad-form-actions">
      <a href="{{ route('admin.schools.index') }}" class="ad-btn ghost">Cancel</a>
      <button class="ad-btn primary" type="submit">{{ $editing ? 'Update School' : 'Create School' }}</button>
    </div>
  </form>
</div>

@if($editing)
<div class="ad-school-links">
  <a href="{{ route('admin.school-categories.index', ['school_id' => $school->id]) }}" class="ad-btn ghost"><i class="bi bi-tags"></i> Manage Categories</a>
  <a href="{{ route('admin.school-products.index', ['school_id' => $school->id]) }}" class="ad-btn ghost"><i class="bi bi-bag"></i> Manage Products</a>
  <a href="{{ route('admin.school-categories.create', ['school_id' => $school->id]) }}" class="ad-btn ghost"><i class="bi bi-plus"></i> Add Category</a>
  <a href="{{ route('admin.school-products.create', ['school_id' => $school->id]) }}" class="ad-btn ghost"><i class="bi bi-plus"></i> Add Product</a>
</div>
@endif

@push('scripts')
<script>
document.querySelector('input[name="accent_colour"]')?.addEventListener('input', function(){
  const txt = this.nextElementSibling;
  if(txt) txt.value = this.value;
});
(function(){
  const toggle = document.getElementById('schoolVatEnabled');
  const wrap = document.getElementById('schoolVatPercentWrap');
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
