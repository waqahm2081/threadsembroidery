@extends('admin.layouts.app')

@section('content')
<div class="ad-grid-2">
  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Order details</h2>
      <span class="ad-pill {{ $order->status }}">{{ ucfirst($order->status) }}</span>
    </div>
    <dl class="ad-dl">
      <div><dt>Order number</dt><dd>{{ $order->order_number }}</dd></div>
      <div><dt>Customer</dt><dd>{{ $order->name }} @if($order->user)<a class="ad-link" href="{{ route('admin.users.edit', $order->user) }}">(account)</a>@endif</dd></div>
      <div><dt>Email</dt><dd>{{ $order->email }}</dd></div>
      <div><dt>Phone</dt><dd>{{ $order->phone ?: '—' }}</dd></div>
      <div><dt>Company</dt><dd>{{ $order->company_name ?: '—' }}</dd></div>
      <div><dt>Notes</dt><dd>{{ $order->notes ?: '—' }}</dd></div>
    </dl>

    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="ad-inline-form" style="margin-top:1.25rem">
      @csrf
      @method('PATCH')
      <label class="ad-label" for="status">Update status</label>
      <div class="ad-inline-row">
        <select class="ad-input" id="status" name="status">
          @foreach($statuses as $s)
            <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
          @endforeach
        </select>
        <button class="ad-btn" type="submit">Save</button>
      </div>
    </form>
  </section>

  <section class="ad-panel">
    <div class="ad-panel-head"><h2>Addresses & totals</h2></div>
    <div class="ad-split">
      <div>
        <h3>Shipping</h3>
        <p>
          {{ $order->shipping_address_line1 }}<br>
          @if($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
          {{ $order->shipping_city }} {{ $order->shipping_postcode }}<br>
          {{ $order->shipping_country }}
        </p>
      </div>
      <div>
        <h3>Billing</h3>
        @if($order->billing_same_as_shipping)
          <p class="muted">Same as shipping</p>
        @else
          <p>
            {{ $order->billing_name }}<br>
            {{ $order->billing_address_line1 }}<br>
            @if($order->billing_address_line2){{ $order->billing_address_line2 }}<br>@endif
            {{ $order->billing_city }} {{ $order->billing_postcode }}<br>
            {{ $order->billing_country }}
          </p>
        @endif
      </div>
    </div>
    <dl class="ad-dl" style="margin-top:1rem">
      <div><dt>Subtotal</dt><dd>£{{ number_format($order->subtotal, 2) }}</dd></div>
      <div><dt>VAT</dt><dd>£{{ number_format($order->vat_amount, 2) }}</dd></div>
      <div><dt>Delivery</dt><dd>£{{ number_format($order->delivery_fee, 2) }}</dd></div>
      <div><dt>Total</dt><dd><b>£{{ number_format($order->total, 2) }}</b></dd></div>
    </dl>
  </section>
</div>

@foreach($order->items as $item)
  <section class="ad-panel" style="margin-top:1rem">
    <div class="ad-panel-head">
      <h2>{{ $item->title }}</h2>
      <span>£{{ number_format($item->line_total, 2) }}</span>
    </div>
    <p class="muted">{{ $item->summary }}</p>
    <dl class="ad-dl">
      <div><dt>Method</dt><dd>{{ $item->method ?: '—' }}</dd></div>
      <div><dt>Colour</dt><dd>{{ $item->colour_name ?: '—' }}</dd></div>
      <div><dt>Qty</dt><dd>{{ $item->quantity }}</dd></div>
    </dl>

    @if(!empty($item->size_breakdown))
      <h3 style="margin:1rem 0 .5rem;font-size:.9rem">Size breakdown</h3>
      <div class="ad-chips">
        @foreach($item->size_breakdown as $size => $qty)
          @if(is_array($qty))
            <span class="ad-chip">{{ $qty['size'] ?? $size }} × {{ $qty['qty'] ?? $qty['quantity'] ?? 0 }}</span>
          @else
            <span class="ad-chip">{{ $size }} × {{ $qty }}</span>
          @endif
        @endforeach
      </div>
    @endif

    @if($item->files->isNotEmpty())
      <h3 style="margin:1rem 0 .5rem;font-size:.9rem">Artwork files</h3>
      <ul class="ad-file-list">
        @foreach($item->files as $file)
          <li>
            <a href="{{ asset('storage/'.$file->path) }}" target="_blank" rel="noopener">
              {{ $file->original_name ?: basename($file->path) }}
            </a>
            @if($file->placement_label)
              <span class="muted"> — {{ $file->placement_label }}</span>
            @endif
          </li>
        @endforeach
      </ul>
    @endif
  </section>
@endforeach

<div style="margin-top:1rem">
  <a class="ad-btn ghost" href="{{ route('admin.orders.index') }}"><i class="bi bi-arrow-left"></i> Back to orders</a>
</div>
@endsection
