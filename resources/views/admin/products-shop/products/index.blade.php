@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <form class="ad-filters" method="GET" action="{{ route('admin.products-shop.products.index') }}">
    <input class="ad-input" type="search" name="q" value="{{ $q }}" placeholder="Search title, style code, SKU, brand…">
    <select class="ad-input" name="category">
      <option value="">All categories</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->slug }}" @selected($categorySlug === $cat->slug)>{{ $cat->name }}</option>
      @endforeach
    </select>
    <button class="ad-btn ghost" type="submit"><i class="bi bi-search"></i> Filter</button>
  </form>
  <a class="ad-btn ghost" href="{{ route('admin.products-shop.categories.index') }}"><i class="bi bi-tags"></i> Categories</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th></th>
          <th>Product</th>
          <th>Style</th>
          <th>Brand</th>
          <th>Variants</th>
          <th>Price range</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td style="width:64px">
              @if(!empty($thumbs[$product->sample_id]))
                <img src="{{ $thumbs[$product->sample_id] }}" alt="" style="width:48px;height:48px;object-fit:contain;border-radius:8px;background:var(--ad-bg-2)">
              @else
                <span class="muted">—</span>
              @endif
            </td>
            <td>
              <b>{{ $product->title }}</b>
              @if($product->type)<div class="muted" style="font-size:.78rem">{{ $product->type }}</div>@endif
            </td>
            <td><code>{{ $product->style_code }}</code></td>
            <td>{{ $product->brand ?: '—' }}</td>
            <td>{{ number_format($product->variant_count) }}</td>
            <td>
              £{{ number_format((float) $product->min_price, 2) }}
              @if((float) $product->max_price > (float) $product->min_price)
                – £{{ number_format((float) $product->max_price, 2) }}
              @endif
            </td>
            <td>
              <a class="ad-btn ghost sm" href="{{ route('admin.products-shop.products.edit', $product->style_code) }}">
                Manage prices
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No products found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="ad-pager">{{ $products->links() }}</div>
</section>
@endsection
