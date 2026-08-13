@extends('admin.layouts.app')

@section('content')
@include('admin.bundles._tabs')

<section class="ad-panel">
  <form method="POST" action="{{ $mode === 'create' ? route('admin.placements.store') : route('admin.placements.update', $placement) }}">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="ad-form-grid">
      <div class="ad-field">
        <label class="ad-label" for="label">Label</label>
        <input class="ad-input" id="label" name="label" value="{{ old('label', $placement->label) }}" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="slug">Slug</label>
        <input class="ad-input" id="slug" name="slug" value="{{ old('slug', $placement->slug) }}" placeholder="auto from label if blank">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="method">Method</label>
        <select class="ad-input" id="method" name="method" required>
          @foreach(['both','print','embroidery'] as $m)
            <option value="{{ $m }}" @selected(old('method', $placement->method) === $m)>{{ ucfirst($m) }}</option>
          @endforeach
        </select>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="price">Price per garment (£)</label>
        <input class="ad-input" id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $placement->price) }}" required>
      </div>
      <div class="ad-field full">
        <label class="ad-label" for="sizes_text">Available sizes (comma-separated)</label>
        <input class="ad-input" id="sizes_text" name="sizes_text" value="{{ old('sizes_text', implode(', ', $placement->sizes ?? [])) }}" placeholder="70mm, 80mm, 90mm">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="sort_order">Sort order</label>
        <input class="ad-input" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $placement->sort_order) }}">
      </div>
      <div class="ad-field">
        <label class="ad-label">Flags</label>
        <label class="ad-check"><input type="checkbox" name="included_in_bundle" value="1" @checked(old('included_in_bundle', $placement->included_in_bundle))> Included in bundle price</label>
        <label class="ad-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $placement->is_active))> Active</label>
      </div>
    </div>

    <div class="ad-form-actions">
      <a class="ad-btn ghost" href="{{ route('admin.placements.index') }}">Cancel</a>
      <button class="ad-btn" type="submit">{{ $mode === 'create' ? 'Create placement' : 'Save changes' }}</button>
    </div>
  </form>
</section>
@endsection
