@extends('admin.layouts.app')
@section('title', 'Product Categories')
@php $nav = 'school-categories'; @endphp

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET">
    <select class="ad-input" name="school_id" onchange="this.form.submit()">
      <option value="">All Schools</option>
      @foreach($schools as $s)
        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
      @endforeach
    </select>
    <input class="ad-input" name="q" value="{{ request('q') }}" placeholder="Search categories…">
    <button class="ad-btn" type="submit"><i class="bi bi-search"></i></button>
  </form>
  <a href="{{ route('admin.school-categories.create', array_filter(['school_id' => request('school_id')])) }}" class="ad-btn primary"><i class="bi bi-plus-lg"></i> Add Category</a>
</div>

<div class="ad-card">
  <table class="ad-table">
    <thead>
      <tr>
        <th>Category</th>
        <th>School</th>
        <th>Description</th>
        <th>Status</th>
        <th>Sort</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($categories as $cat)
      <tr>
        <td><b>{{ $cat->name }}</b></td>
        <td>{{ $cat->school?->name }}</td>
        <td>{{ \Illuminate\Support\Str::limit($cat->description, 60) }}</td>
        <td><span class="ad-badge {{ $cat->is_active ? 'green' : 'grey' }}">{{ $cat->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td>{{ $cat->sort_order }}</td>
        <td class="ad-actions">
          <a href="{{ route('admin.school-products.index', ['school_id' => $cat->school_id]) }}" class="ad-btn sm ghost" title="View products"><i class="bi bi-bag"></i></a>
          <a href="{{ route('admin.school-categories.edit', $cat) }}" class="ad-btn sm ghost"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="{{ route('admin.school-categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')">
            @csrf @method('DELETE')
            <button class="ad-btn sm ghost danger" type="submit"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="ad-empty">No categories found.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($categories->hasPages())
  <div class="ad-pagination">{{ $categories->links() }}</div>
  @endif
</div>
@endsection
