@extends('admin.layouts.app')

@section('content')
@include('admin.bundles._tabs')

<section class="ad-panel">
  <form method="POST" action="{{ $mode === 'create' ? route('admin.colours.store') : route('admin.colours.update', $colour) }}">
    @csrf
    @if($mode === 'edit') @method('PUT') @endif

    <div class="ad-form-grid">
      <div class="ad-field">
        <label class="ad-label" for="name">Name</label>
        <input class="ad-input" id="name" name="name" value="{{ old('name', $colour->name) }}" required>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="slug">Slug</label>
        <input class="ad-input" id="slug" name="slug" value="{{ old('slug', $colour->slug) }}" placeholder="auto from name if blank">
      </div>
      <div class="ad-field">
        <label class="ad-label" for="hex">Hex</label>
        <div style="display:flex;gap:.5rem;align-items:center">
          <input class="ad-input" id="hex" name="hex" value="{{ old('hex', $colour->hex) }}" required style="flex:1">
          <input type="color" value="{{ old('hex', $colour->hex) }}" oninput="document.getElementById('hex').value=this.value" style="width:44px;height:38px;border:none;background:transparent;cursor:pointer">
        </div>
      </div>
      <div class="ad-field">
        <label class="ad-label" for="sort_order">Sort order</label>
        <input class="ad-input" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $colour->sort_order) }}">
      </div>
      <div class="ad-field">
        <label class="ad-label">Flags</label>
        <label class="ad-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $colour->is_active))> Active</label>
      </div>
    </div>

    <div class="ad-form-actions">
      <a class="ad-btn ghost" href="{{ route('admin.colours.index') }}">Cancel</a>
      <button class="ad-btn" type="submit">{{ $mode === 'create' ? 'Create colour' : 'Save changes' }}</button>
    </div>
  </form>
</section>
@endsection
