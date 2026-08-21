@extends('admin.layouts.app')
@section('title', $editing ? 'Edit School' : 'New School')
@php $nav = 'schools'; @endphp

@section('content')
<div class="ad-card" style="max-width:680px">
  <form method="POST" action="{{ $editing ? route('admin.schools.update', $school) : route('admin.schools.store') }}" enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="ad-form-grid">
      <div class="ad-fg" style="grid-column:1/-1">
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

      <div class="ad-fg" style="grid-column:1/-1">
        <label class="ad-label">Address</label>
        <input class="ad-input" name="address" value="{{ old('address', $school->address) }}">
      </div>

      <div class="ad-fg" style="grid-column:1/-1">
        <label class="ad-label">Description</label>
        <textarea class="ad-input" name="description" rows="3" style="resize:vertical">{{ old('description', $school->description) }}</textarea>
      </div>

      <div class="ad-fg">
        <label class="ad-label">Accent Colour</label>
        <div style="display:flex;gap:.5rem;align-items:center">
          <input type="color" name="accent_colour" value="{{ old('accent_colour', $school->accent_colour ?: '#e85820') }}" style="width:48px;height:38px;border-radius:8px;border:1px solid var(--ad-line);padding:2px;background:none;cursor:pointer">
          <input class="ad-input" name="accent_colour_text" value="{{ old('accent_colour', $school->accent_colour ?: '#e85820') }}" style="font-family:monospace" placeholder="#e85820" oninput="this.previousElementSibling.value=this.value">
        </div>
      </div>

      <div class="ad-fg">
        <label class="ad-label">Sort Order</label>
        <input class="ad-input" name="sort_order" type="number" min="0" value="{{ old('sort_order', $school->sort_order ?? 0) }}">
      </div>

      <div class="ad-fg" style="grid-column:1/-1">
        <label class="ad-label">School Logo</label>
        @if($editing && $school->logo_url)
          <div style="margin-bottom:.7rem;display:flex;align-items:center;gap:.9rem">
            <img src="{{ $school->logo_url }}" alt="" style="height:60px;object-fit:contain;border-radius:8px;background:var(--ad-surface-2);padding:6px">
            <label style="display:flex;align-items:center;gap:.4rem;font-size:.85rem;color:var(--ad-muted);cursor:pointer">
              <input type="checkbox" name="remove_logo" value="1"> Remove logo
            </label>
          </div>
        @endif
        <input class="ad-input" type="file" name="logo" accept="image/*">
      </div>

      <div class="ad-fg">
        <label class="ad-label" style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $school->is_active ?? true) ? 'checked' : '' }}>
          Active
        </label>
      </div>
    </div>

    <div class="ad-form-actions">
      <a href="{{ route('admin.schools.index') }}" class="ad-btn ghost">Cancel</a>
      <button class="ad-btn primary" type="submit">{{ $editing ? 'Update School' : 'Create School' }}</button>
    </div>
  </form>
</div>

@if($editing)
<div style="margin-top:1.4rem;display:flex;gap:.8rem;flex-wrap:wrap">
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
</script>
@endpush
@endsection
