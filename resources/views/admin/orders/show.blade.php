@extends('admin.layouts.app')

@section('content')
<div class="ad-grid-2">
  <section class="ad-panel">
    <div class="ad-panel-head">
      <h2>Order details</h2>
      <span class="ad-pill {{ $order->status }}">{{ \App\Support\OrderFulfillmentStatus::label($order->status) }}</span>
    </div>
    <dl class="ad-dl">
      <div><dt>Order number</dt><dd>{{ $order->order_number }}</dd></div>
      <div><dt>Customer</dt><dd>{{ $order->name }} @if($order->user)<a class="ad-link" href="{{ route('admin.users.edit', $order->user) }}">(account)</a>@endif</dd></div>
      <div><dt>Email</dt><dd>{{ $order->email }}</dd></div>
      <div><dt>Phone</dt><dd>{{ $order->phone ?: '—' }}</dd></div>
      <div><dt>Company</dt><dd>{{ $order->company_name ?: '—' }}</dd></div>
      <div><dt>Tracking</dt><dd>{{ $order->tracking_number ?: '—' }}</dd></div>
      <div><dt>Notes</dt><dd>{{ $order->notes ?: '—' }}</dd></div>
    </dl>

    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="ad-inline-form js-order-status-form" style="margin-top:1.25rem">
      @csrf
      @method('PATCH')
      <label class="ad-label" for="status">Update status</label>
      <select class="ad-input js-order-status" id="status" name="status">
        @foreach($statuses as $value => $label)
          <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
        @endforeach
      </select>
      <div class="js-tracking-wrap" style="margin-top:.75rem;{{ $order->status === 'dispatched' ? '' : 'display:none' }}">
        <label class="ad-label" for="tracking_number">Tracking number</label>
        <input class="ad-input" id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Carrier tracking / reference">
        @error('tracking_number')<p class="ad-error">{{ $message }}</p>@enderror
      </div>
      <p class="ad-field-hint" style="margin-top:.55rem">Saving will email the customer with this status update.</p>
      <button class="ad-btn" type="submit" style="margin-top:.75rem">Save &amp; notify customer</button>
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

@push('scripts')
<script>
(function(){
  document.querySelectorAll('.js-order-status-form').forEach((form) => {
    const select = form.querySelector('.js-order-status');
    const wrap = form.querySelector('.js-tracking-wrap');
    const input = form.querySelector('[name="tracking_number"]');
    if (!select || !wrap) return;
    const sync = () => {
      const show = select.value === 'dispatched';
      wrap.style.display = show ? '' : 'none';
      if (input) input.required = show;
    };
    select.addEventListener('change', sync);
    sync();
  });
})();
</script>
@endpush
