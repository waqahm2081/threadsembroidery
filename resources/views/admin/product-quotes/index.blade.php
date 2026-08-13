@extends('admin.layouts.app')

@section('content')
<div class="ad-toolbar">
  <p class="muted" style="margin:0">Quotes saved from the Product Customizer.</p>
  <a class="ad-btn" href="{{ route('admin.customizer') }}"><i class="bi bi-plus-lg"></i> New quote</a>
</div>

<section class="ad-panel">
  <div class="ad-table-wrap">
    <table class="ad-table">
      <thead>
        <tr>
          <th>Preview</th>
          <th>Reference</th>
          <th>Product</th>
          <th>Method</th>
          <th>Qty</th>
          <th>Total</th>
          <th>Date</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($quotes as $quote)
          <tr>
            <td>
              <a href="{{ route('admin.product-quotes.show', $quote) }}" class="ad-quote-thumb">
                <img src="{{ $quote->preview_url }}" alt="">
              </a>
            </td>
            <td>
              <a href="{{ route('admin.product-quotes.show', $quote) }}"><b>{{ $quote->reference }}</b></a>
              @if($quote->user)
                <div class="muted">by {{ $quote->user->name }}</div>
              @endif
            </td>
            <td>
              <b>{{ $quote->product_title }}</b>
              <div class="muted">{{ $quote->colour_name ?: '—' }} · {{ $quote->product_style_code }}</div>
            </td>
            <td>{{ $quote->methodLabel() }}</td>
            <td>{{ $quote->quantity }}</td>
            <td>£{{ number_format($quote->subtotal, 2) }}</td>
            <td>{{ optional($quote->created_at)->format('d M Y H:i') }}</td>
            <td class="ad-actions">
              <a class="ad-btn ghost sm" href="{{ route('admin.product-quotes.show', $quote) }}">View</a>
              <form method="POST" action="{{ route('admin.product-quotes.destroy', $quote) }}" onsubmit="return confirm('Delete this quote?')">
                @csrf
                @method('DELETE')
                <button class="ad-btn danger sm" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="muted">No product quotes yet. Create one in Product Customizer.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="ad-pager">{{ $quotes->links() }}</div>
</section>
@endsection
