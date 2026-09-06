@extends('admin.layouts.app')

@section('content')
@include('admin.bundles._tabs')

<section class="ad-panel">
  <form method="POST" action="{{ $mode === 'create' ? route('admin.bundles.store') : route('admin.bundles.update', $bundle) }}">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="ad-form-grid">
      <div class="ad-field">
        <label class="ad-label" for="name">Name</label>
        <input class="ad-input" id="name" name="name" value="{{ old('name', $bundle->name) }}" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="slug">Slug</label>
        <input class="ad-input" id="slug" name="slug" value="{{ old('slug', $bundle->slug) }}" placeholder="auto from name if blank">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="badge">Badge</label>
        <input class="ad-input" id="badge" name="badge" value="{{ old('badge', $bundle->badge) }}" placeholder="Most Popular">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="tagline">Tagline</label>
        <input class="ad-input" id="tagline" name="tagline" value="{{ old('tagline', $bundle->tagline) }}">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="sort_order">Sort order</label>
        <input class="ad-input" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $bundle->sort_order) }}">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="price">Price (£)</label>
        <input class="ad-input" id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $bundle->price) }}" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="save_amount">Save amount (£)</label>
        <input class="ad-input" id="save_amount" type="number" step="0.01" min="0" name="save_amount" value="{{ old('save_amount', $bundle->save_amount) }}">
      </div>
      <div class="ad-field full">
        <label class="ad-label" for="blurb">Blurb</label>
        <textarea class="ad-input" id="blurb" name="blurb" rows="3">{{ old('blurb', $bundle->blurb) }}</textarea>
      </div>
      <div class="ad-field">
        <label class="ad-label">Methods</label>
        @php $methods = old('methods', $bundle->methods ?? []); @endphp
        <label class="ad-check"><input type="checkbox" name="methods[]" value="embroidery" @checked(in_array('embroidery', $methods, true))> Embroidery</label>
        <label class="ad-check"><input type="checkbox" name="methods[]" value="print" @checked(in_array('print', $methods, true))> Print</label>
      </div>
      <div class="ad-field">
        <label class="ad-label">Flags</label>
        <label class="ad-check"><input type="checkbox" name="featured" value="1" @checked(old('featured', $bundle->featured))> Featured</label>
        <label class="ad-check"><input type="checkbox" name="popular" value="1" @checked(old('popular', $bundle->popular))> Popular</label>
        <label class="ad-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $bundle->is_active))> Active on storefront</label>
      </div>
    </div>

    <h3 class="ad-section-title">Includes</h3>
    <div id="includesList" class="ad-repeat">
      @foreach(old('includes', $includes) as $i => $text)
        <div class="ad-repeat-row">
          <input class="ad-input" name="includes[]" value="{{ $text }}" placeholder="e.g. 3 × Premium Polo Shirts">
          <button type="button" class="ad-btn ghost sm" data-remove>Remove</button>
        </div>
      @endforeach
    </div>
    <button type="button" class="ad-btn ghost sm" id="addInclude"><i class="bi bi-plus"></i> Add include</button>

    <h3 class="ad-section-title">Garments</h3>
    <p class="muted" style="margin:0 0 .8rem">Assign available colours for each garment. Manage the palette under the Colours tab.</p>
    <div id="garmentsList" class="ad-repeat">
      @foreach(old('garments', $garments) as $i => $g)
        @php $selectedColours = collect($g['colour_ids'] ?? [])->map(fn ($id) => (int) $id)->all(); @endphp
        <div class="ad-repeat-row ad-garment-row" style="flex-wrap:wrap;align-items:flex-start">
          <input class="ad-input" name="garments[{{ $i }}][label]" value="{{ $g['label'] ?? '' }}" placeholder="Label">
          <input class="ad-input" name="garments[{{ $i }}][category_slug]" value="{{ $g['category_slug'] ?? '' }}" placeholder="Category slug" list="catList">
          <input class="ad-input" type="number" min="1" name="garments[{{ $i }}][qty]" value="{{ $g['qty'] ?? 1 }}" placeholder="Qty">
          <select class="ad-input" name="garments[{{ $i }}][size_guide_key]">
            <option value="">Size guide</option>
            @foreach($sizeGuides as $guide)
              <option value="{{ $guide->key }}" @selected(($g['size_guide_key'] ?? '') === $guide->key)>{{ $guide->code }} — {{ $guide->name }}</option>
            @endforeach
          </select>
          <button type="button" class="ad-btn ghost sm" data-remove>Remove</button>
          <div class="ad-garment-colours" style="flex:1 1 100%;display:flex;flex-wrap:wrap;gap:.45rem .75rem;padding:.35rem 0 .2rem">
            @forelse($allColours as $colour)
              <label class="ad-check" style="margin:0;font-size:.82rem">
                <input type="checkbox" name="garments[{{ $i }}][colour_ids][]" value="{{ $colour->id }}" @checked(in_array((int) $colour->id, $selectedColours, true))>
                <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $colour->hex }};border:1px solid rgba(255,255,255,.2);vertical-align:middle;margin-right:.25rem"></span>
                {{ $colour->name }}
              </label>
            @empty
              <span class="muted" style="font-size:.82rem">No colours yet — add some under the Colours tab.</span>
            @endforelse
          </div>
        </div>
      @endforeach
    </div>
    <datalist id="catList">
      <option value="polos"><option value="t-shirts"><option value="sweatshirts"><option value="hoods"><option value="jackets"><option value="hi-vis">
    </datalist>
    <button type="button" class="ad-btn ghost sm" id="addGarment"><i class="bi bi-plus"></i> Add garment</button>

    <div class="ad-form-actions">
      <a class="ad-btn ghost" href="{{ route('admin.bundles.index') }}">Cancel</a>
      <button class="ad-btn" type="submit">{{ $mode === 'create' ? 'Create bundle' : 'Save changes' }}</button>
    </div>
  </form>
</section>

<template id="includeTpl">
  <div class="ad-repeat-row">
    <input class="ad-input" name="includes[]" placeholder="e.g. 3 × Premium Polo Shirts">
    <button type="button" class="ad-btn ghost sm" data-remove>Remove</button>
  </div>
</template>
<template id="garmentTpl">
  <div class="ad-repeat-row ad-garment-row" style="flex-wrap:wrap;align-items:flex-start">
    <input class="ad-input" data-name="label" placeholder="Label">
    <input class="ad-input" data-name="category_slug" placeholder="Category slug" list="catList">
    <input class="ad-input" type="number" min="1" data-name="qty" value="1" placeholder="Qty">
    <select class="ad-input" data-name="size_guide_key">
      <option value="">Size guide</option>
      @foreach($sizeGuides as $guide)
        <option value="{{ $guide->key }}">{{ $guide->code }} — {{ $guide->name }}</option>
      @endforeach
    </select>
    <button type="button" class="ad-btn ghost sm" data-remove>Remove</button>
    <div class="ad-garment-colours" data-colours style="flex:1 1 100%;display:flex;flex-wrap:wrap;gap:.45rem .75rem;padding:.35rem 0 .2rem">
      @foreach($allColours as $colour)
        <label class="ad-check" style="margin:0;font-size:.82rem">
          <input type="checkbox" data-colour-id="{{ $colour->id }}" value="{{ $colour->id }}">
          <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $colour->hex }};border:1px solid rgba(255,255,255,.2);vertical-align:middle;margin-right:.25rem"></span>
          {{ $colour->name }}
        </label>
      @endforeach
    </div>
  </div>
</template>
<script>
(() => {
  const includes = document.getElementById('includesList');
  const garments = document.getElementById('garmentsList');
  document.getElementById('addInclude')?.addEventListener('click', () => {
    includes.appendChild(document.getElementById('includeTpl').content.cloneNode(true));
  });
  document.getElementById('addGarment')?.addEventListener('click', () => {
    const idx = garments.querySelectorAll('.ad-garment-row').length;
    const node = document.getElementById('garmentTpl').content.cloneNode(true);
    node.querySelectorAll('[data-name]').forEach(el => {
      el.name = `garments[${idx}][${el.dataset.name}]`;
      el.removeAttribute('data-name');
    });
    node.querySelectorAll('[data-colour-id]').forEach(el => {
      el.name = `garments[${idx}][colour_ids][]`;
      el.removeAttribute('data-colour-id');
    });
    garments.appendChild(node);
  });
  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-remove]');
    if (!btn) return;
    const row = btn.closest('.ad-repeat-row');
    if (row) row.remove();
  });
})();
</script>
@endsection
