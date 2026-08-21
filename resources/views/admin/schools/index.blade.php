@extends('admin.layouts.app')
@section('title', 'Schools')
@php $nav = 'schools'; @endphp

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET">
    <input class="ad-input" name="q" value="{{ request('q') }}" placeholder="Search schools…">
    <button class="ad-btn" type="submit"><i class="bi bi-search"></i></button>
  </form>
  <a href="{{ route('admin.schools.create') }}" class="ad-btn primary"><i class="bi bi-plus-lg"></i> Add School</a>
</div>

<div class="ad-card">
  <table class="ad-table">
    <thead>
      <tr>
        <th>School</th>
        <th>Categories</th>
        <th>Products</th>
        <th>Status</th>
        <th>Sort</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($schools as $school)
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:.8rem">
            @if($school->logo_url)
              <img src="{{ $school->logo_url }}" alt="" style="width:40px;height:40px;object-fit:contain;border-radius:8px;background:var(--ad-surface-2);padding:4px">
            @else
              <div style="width:40px;height:40px;border-radius:8px;background:var(--ad-brand-soft);display:grid;place-items:center;color:var(--ad-brand-hi);font-weight:800;font-size:1.1rem">{{ strtoupper(substr($school->name,0,1)) }}</div>
            @endif
            <div>
              <b style="display:block">{{ $school->name }}</b>
              @if($school->contact_email)<span class="ad-muted" style="font-size:.8rem">{{ $school->contact_email }}</span>@endif
            </div>
          </div>
        </td>
        <td>{{ $school->categories_count }}</td>
        <td>{{ $school->products_count }}</td>
        <td><span class="ad-badge {{ $school->is_active ? 'green' : 'grey' }}">{{ $school->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td>{{ $school->sort_order }}</td>
        <td class="ad-actions">
          <a href="{{ route('admin.school-categories.index', ['school_id' => $school->id]) }}" class="ad-btn sm ghost" title="Categories"><i class="bi bi-tags"></i></a>
          <a href="{{ route('admin.school-products.index', ['school_id' => $school->id]) }}" class="ad-btn sm ghost" title="Products"><i class="bi bi-bag"></i></a>
          <a href="{{ route('admin.schools.edit', $school) }}" class="ad-btn sm ghost"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="{{ route('admin.schools.destroy', $school) }}" onsubmit="return confirm('Delete this school and all its products?')">
            @csrf @method('DELETE')
            <button class="ad-btn sm ghost danger" type="submit"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="ad-empty">No schools found.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($schools->hasPages())
  <div class="ad-pagination">{{ $schools->links() }}</div>
  @endif
</div>
@endsection
