@extends('admin.layouts.app')
@section('title', $editing ? 'Edit Product' : 'New Product')
@php $nav = 'school-products'; @endphp

@section('content')
<div style="display:grid;grid-template-columns:1fr 360px;gap:1.4rem;align-items:start">

  {{-- Main form --}}
  <form method="POST"
    action="{{ $editing ? route('admin.school-products.update', $product) : route('admin.school-products.store') }}"
    enctype="multipart/form-data" id="productForm">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="ad-card">
      <h3 class="ad-card-title">Product Details</h3>
      <div class="ad-form-grid">
        <div class="ad-fg" style="grid-column:1/-1">
          <label class="ad-label">School <span class="req">*</span></label>
          <select class="ad-input @error('school_id') is-invalid @enderror" name="school_id" id="schoolSelect" required>
            <option value="">— Select School —</option>
            @foreach($schools as $s)
              <option value="{{ $s->id }}"
                data-categories="{{ $s->allCategories->map(fn($c) => ['id'=>$c->id,'name'=>$c->name])->toJson() }}"
                {{ old('school_id', $product->school_id ?? $selectedSchool?->id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
          </select>
          @error('school_id')<p class="ad-error">{{ $message }}</p>@enderror
        </div>

        <div class="ad-fg" style="grid-column:1/-1">
          <label class="ad-label">Category</label>
          <select class="ad-input" name="school_product_category_id" id="categorySelect">
            <option value="">— No Category —</option>
            @if($editing)
              @foreach($selectedSchool?->allCategories ?? [] as $cat)
                <option value="{{ $cat->id }}" {{ $product->school_product_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            @endif
          </select>
        </div>

        <div class="ad-fg" style="grid-column:1/-1">
          <label class="ad-label">Product Name <span class="req">*</span></label>
          <input class="ad-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', $product->name) }}" required>
          @error('name')<p class="ad-error">{{ $message }}</p>@enderror
        </div>

        <div class="ad-fg" style="grid-column:1/-1">
          <label class="ad-label">Description</label>
          <textarea class="ad-input" name="description" rows="4" style="resize:vertical">{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="ad-fg">
          <label class="ad-label">Sort Order</label>
          <input class="ad-input" name="sort_order" type="number" min="0" value="{{ old('sort_order', $product->sort_order ?? 0) }}">
        </div>
        <div class="ad-fg">
          <label class="ad-label" style="display:flex;align-items:center;gap:.5rem;cursor:pointer;margin-top:1.6rem">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
            Active
          </label>
        </div>
      </div>
    </div>

    {{-- Sizes & Prices --}}
    <div class="ad-card" style="margin-top:1.2rem">
      <h3 class="ad-card-title">Sizes & Prices</h3>
      <table class="ad-table" id="variantsTable">
        <thead>
          <tr><th>Size</th><th>Price (£)</th><th></th></tr>
        </thead>
        <tbody id="variantsBody">
          @php $variants = old('variants', $editing ? $product->variants->toArray() : []) @endphp
          @foreach($variants as $i => $v)
          <tr class="variant-row">
            <td><input type="hidden" name="variants[{{ $i }}][id]" value="{{ $v['id'] ?? '' }}">
              <input class="ad-input" name="variants[{{ $i }}][size]" value="{{ $v['size'] }}" placeholder="e.g. Age 5-6" required></td>
            <td><input class="ad-input" name="variants[{{ $i }}][price]" type="number" step="0.01" min="0" value="{{ number_format($v['price'],2,'.','') }}" required></td>
            <td><button type="button" class="ad-btn sm ghost danger remove-variant"><i class="bi bi-trash"></i></button></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      <button type="button" class="ad-btn ghost" style="margin-top:.8rem" id="addVariantBtn"><i class="bi bi-plus"></i> Add Size</button>
    </div>

    <div class="ad-form-actions">
      <a href="{{ route('admin.school-products.index') }}" class="ad-btn ghost">Cancel</a>
      <button class="ad-btn primary" type="submit">{{ $editing ? 'Update Product' : 'Create Product' }}</button>
    </div>
  </form>

  {{-- Images --}}
  <div>
    <div class="ad-card">
      <h3 class="ad-card-title">Product Images</h3>
      <form method="POST"
        action="{{ $editing ? route('admin.school-products.update', $product) : route('admin.school-products.store') }}"
        enctype="multipart/form-data" id="imgForm">
        @csrf
        @if($editing) @method('PUT') @endif

        {{-- Existing images --}}
        @if($editing && $product->images->isNotEmpty())
        <div class="sp-img-grid" id="imgGrid">
          @foreach($product->images as $img)
          <div class="sp-img-item" data-id="{{ $img->id }}">
            <img src="{{ asset('storage/'.$img->path) }}" alt="">
            <div class="sp-img-actions">
              <label title="Set as primary" style="cursor:pointer">
                <input type="radio" name="primary_image_id" value="{{ $img->id }}" {{ $img->is_primary ? 'checked' : '' }} form="imgForm"> Primary
              </label>
              <label title="Delete" style="cursor:pointer;color:#d94a4a">
                <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" form="imgForm"> Delete
              </label>
            </div>
          </div>
          @endforeach
        </div>
        @endif

        <div style="margin-top:.8rem">
          <label class="ad-label">Upload Images</label>
          <input class="ad-input" type="file" name="images[]" multiple accept="image/*" form="imgForm">
          <p class="ad-muted" style="font-size:.78rem;margin-top:.3rem">First image becomes the primary if no images exist yet.</p>
        </div>

        @if($editing)
        <button type="submit" form="imgForm" class="ad-btn ghost" style="margin-top:.8rem;width:100%"><i class="bi bi-image"></i> Save Images</button>
        @endif
      </form>
    </div>
  </div>
</div>

@push('styles')
<style>
.sp-img-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:.6rem}
.sp-img-item{border-radius:8px;overflow:hidden;border:1px solid var(--ad-line);background:var(--ad-surface-2)}
.sp-img-item img{width:100%;aspect-ratio:1;object-fit:cover;display:block}
.sp-img-actions{padding:.4rem .5rem;display:flex;justify-content:space-between;font-size:.75rem;flex-wrap:wrap;gap:.3rem}
</style>
@endpush

@push('scripts')
<script>
const schoolData = {};
document.querySelectorAll('#schoolSelect option[data-categories]').forEach(opt => {
  try { schoolData[opt.value] = JSON.parse(opt.dataset.categories); } catch(e){}
});

function rebuildCategories(schoolId, selectedId){
  const sel = document.getElementById('categorySelect');
  sel.innerHTML = '<option value="">— No Category —</option>';
  const cats = schoolData[schoolId] || [];
  cats.forEach(c => {
    const o = document.createElement('option');
    o.value = c.id; o.textContent = c.name;
    if(String(c.id) === String(selectedId)) o.selected = true;
    sel.appendChild(o);
  });
}

document.getElementById('schoolSelect')?.addEventListener('change', function(){
  rebuildCategories(this.value, null);
});
rebuildCategories(document.getElementById('schoolSelect')?.value, '{{ $product->school_product_category_id ?? '' }}');

// Variants
let vIdx = {{ count($variants ?? []) }};
document.getElementById('addVariantBtn')?.addEventListener('click', () => {
  const body = document.getElementById('variantsBody');
  const row = document.createElement('tr');
  row.className = 'variant-row';
  row.innerHTML = `<td><input type="hidden" name="variants[${vIdx}][id]" value=""><input class="ad-input" name="variants[${vIdx}][size]" placeholder="e.g. Age 5-6" required></td><td><input class="ad-input" name="variants[${vIdx}][price]" type="number" step="0.01" min="0" placeholder="0.00" required></td><td><button type="button" class="ad-btn sm ghost danger remove-variant"><i class="bi bi-trash"></i></button></td>`;
  body.appendChild(row);
  vIdx++;
  row.querySelector('input[name$="[size]"]').focus();
});

document.getElementById('variantsBody')?.addEventListener('click', e => {
  if(e.target.closest('.remove-variant')){
    e.target.closest('tr').remove();
  }
});
</script>
@endpush
@endsection
