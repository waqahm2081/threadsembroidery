@extends('admin.layouts.app')
@section('title', $editing ? 'Edit Category' : 'New Category')
@php $nav = 'school-categories'; @endphp

@section('content')
<div class="ad-card" style="max-width:540px">
  <form method="POST" action="{{ $editing ? route('admin.school-categories.update', $category) : route('admin.school-categories.store') }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="ad-form-grid">
      <div class="ad-fg" style="grid-column:1/-1">
        <label class="ad-label">School <span class="req">*</span></label>
        <select class="ad-input @error('school_id') is-invalid @enderror" name="school_id" required>
          <option value="">— Select School —</option>
          @foreach($schools as $s)
            <option value="{{ $s->id }}" {{ old('school_id', $category->school_id ?? $selectedSchool?->id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
          @endforeach
        </select>
        @error('school_id')<p class="ad-error">{{ $message }}</p>@enderror
      </div>

      <div class="ad-fg" style="grid-column:1/-1">
        <label class="ad-label">Category Name <span class="req">*</span></label>
        <input class="ad-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', $category->name) }}" required>
        @error('name')<p class="ad-error">{{ $message }}</p>@enderror
      </div>

      <div class="ad-fg" style="grid-column:1/-1">
        <label class="ad-label">Description</label>
        <input class="ad-input" name="description" value="{{ old('description', $category->description) }}">
      </div>

      <div class="ad-fg">
        <label class="ad-label">Sort Order</label>
        <input class="ad-input" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
      </div>

      <div class="ad-fg">
        <label class="ad-label" style="display:flex;align-items:center;gap:.5rem;cursor:pointer">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
          Active
        </label>
      </div>
    </div>

    <div class="ad-form-actions">
      <a href="{{ route('admin.school-categories.index') }}" class="ad-btn ghost">Cancel</a>
      <button class="ad-btn primary" type="submit">{{ $editing ? 'Update' : 'Create Category' }}</button>
    </div>
  </form>
</div>
@endsection
