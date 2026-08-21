@extends('admin.layouts.app')
@section('title', 'Shop Products')
@php $nav = 'school-products'; @endphp

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET">
    <select class="ad-input" name="school_id" onchange="this.form.submit()">
      <option value="">All Schools</option>
      @foreach($schools as $s)
        <option value="{{ $s->id }}" {{ request('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
      @endforeach
    </select>
    <input class="ad-input" name="q" value="{{ request('q') }}" placeholder="Search products…">
    <button class="ad-btn" type="submit"><i class="bi bi-search"></i></button>
  </form>
  <a href="{{ route('admin.school-products.create', array_filter(['school_id' => request('school_id')])) }}" class="ad-btn primary"><i class="bi bi-plus-lg"></i> Add Product</a>
</div>

<div class="ad-card">
  <table class="ad-table">
    <thead>
      <tr>
        <th>Product</th>
        <th>School</th>
        <th>Category</th>
        <th>Sizes</th>
        <th>From</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($products as $product)
      <tr>
        <td>
          <div style="display:flex;align-items:center;gap:.7rem">
            @php $img = $product->images->firstWhere('is_primary', true) ?? $product->images->first() @endphp
            @if($img)
              <img src="{{ asset('storage/'.$img->path) }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;background:var(--ad-surface-2)">
            @else
              <div style="width:40px;height:40px;border-radius:8px;background:var(--ad-surface-2);display:grid;place-items:center;color:var(--ad-muted)"><i class="bi bi-image"></i></div>
            @endif
            <b>{{ $product->name }}</b>
          </div>
        </td>
        <td>{{ $product->school?->name }}</td>
        <td>{{ $product->category?->name ?? '—' }}</td>
        <td>{{ $product->variants_count }}</td>
        <td>
          @if($product->variants_count > 0 && $product->variants_min_price !== null)
            £{{ number_format($product->variants_min_price, 2) }}
          @else
            —
          @endif
        </td>
        <td><span class="ad-badge {{ $product->is_active ? 'green' : 'grey' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></td>
        <td class="ad-actions">
          <a href="{{ route('admin.school-products.edit', $product) }}" class="ad-btn sm ghost"><i class="bi bi-pencil"></i></a>
          <form method="POST" action="{{ route('admin.school-products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
            @csrf @method('DELETE')
            <button class="ad-btn sm ghost danger" type="submit"><i class="bi bi-trash"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="ad-empty">No products found.</td></tr>
      @endforelse
    </tbody>
  </table>
  @if($products->hasPages())
  <div class="ad-pagination">{{ $products->links() }}</div>
  @endif
</div>
@endsection
