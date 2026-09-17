@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET" action="{{ route('admin.products-shop.categories.index') }}">
    <input class="ad-input" type="search" name="q" value="{{ $q }}" placeholder="Search categories…">
    <button class="ad-btn ghost" type="submit"><i class="bi bi-search"></i> Filter</button>
  </form>
  <a class="ad-btn ghost" href="{{ route('admin.products-shop.products.index') }}"><i class="bi bi-bag"></i> Products</a>
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
            <td><a class="ad-btn ghost sm" href="{{ route('admin.products-shop.categories.edit', $category) }}">Edit VAT</a></td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No categories found. Import PenCarrie products first.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="ad-pager">{{ $categories->links() }}</div>
</section>
@endsection
