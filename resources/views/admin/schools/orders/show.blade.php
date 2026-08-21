@extends('admin.layouts.app')
@section('title', 'Uniform Order '.$order->order_number)
@php $nav = 'school-uniform-orders'; @endphp

@section('content')
<div style="display:grid;grid-template-columns:1fr 300px;gap:1.4rem;align-items:start">
  <div>
    <div class="ad-card">
      <h3 class="ad-card-title">Order Items</h3>
      <table class="ad-table">
        <thead><tr><th>Product</th><th>Size</th><th>Qty</th><th>Unit</th><th>Line Total</th></tr></thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->size }}</td>
            <td>{{ $item->quantity }}</td>
            <td>£{{ number_format($item->unit_price, 2) }}</td>
            <td><b>£{{ number_format($item->line_total, 2) }}</b></td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr><td colspan="4" style="text-align:right;font-weight:600">Subtotal</td><td>£{{ number_format($order->subtotal,2) }}</td></tr>
          <tr><td colspan="4" style="text-align:right;font-weight:600">VAT</td><td>£{{ number_format($order->vat_amount,2) }}</td></tr>
          <tr><td colspan="4" style="text-align:right;font-weight:600">Delivery</td><td>£{{ number_format($order->delivery_fee,2) }}</td></tr>
          <tr><td colspan="4" style="text-align:right;font-weight:700;font-size:1rem">Total</td><td><b>£{{ number_format($order->total,2) }}</b></td></tr>
        </tfoot>
      </table>
    </div>

    <div class="ad-card" style="margin-top:1.2rem">
      <h3 class="ad-card-title">Customer & Shipping</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem 1.4rem">
        <div><span class="ad-muted" style="font-size:.78rem">Name</span><b style="display:block">{{ $order->customer_name }}</b></div>
        <div><span class="ad-muted" style="font-size:.78rem">Email</span><b style="display:block">{{ $order->customer_email }}</b></div>
        @if($order->customer_phone)<div><span class="ad-muted" style="font-size:.78rem">Phone</span><b style="display:block">{{ $order->customer_phone }}</b></div>@endif
        @if($order->company_name)<div><span class="ad-muted" style="font-size:.78rem">Company</span><b style="display:block">{{ $order->company_name }}</b></div>@endif
        <div style="grid-column:1/-1"><span class="ad-muted" style="font-size:.78rem">Shipping Address</span>
          <b style="display:block">{{ implode(', ', array_filter([$order->shipping_address_line1,$order->shipping_address_line2,$order->shipping_city,$order->shipping_postcode,$order->shipping_country])) ?: 'Not provided' }}</b>
        </div>
        <div style="grid-column:1/-1"><span class="ad-muted" style="font-size:.78rem">Billing</span>
          @if($order->billing_same_as_shipping)
            <b style="display:block">Same as shipping</b>
          @else
            <b style="display:block">{{ implode(', ', array_filter([$order->billing_name, $order->billing_company_name, $order->billing_address_line1, $order->billing_address_line2, $order->billing_city, $order->billing_postcode, $order->billing_country])) }}</b>
          @endif
        </div>
        @if($order->notes)<div style="grid-column:1/-1"><span class="ad-muted" style="font-size:.78rem">Notes</span><p style="margin:.3rem 0 0">{{ $order->notes }}</p></div>@endif
      </div>
    </div>
  </div>

  <div>
    <div class="ad-card">
      <h3 class="ad-card-title">Details</h3>
      <dl style="display:grid;gap:.6rem">
        <div><dt class="ad-muted" style="font-size:.78rem">Order Number</dt><dd><b>{{ $order->order_number }}</b></dd></div>
        <div><dt class="ad-muted" style="font-size:.78rem">School</dt><dd>{{ $order->school?->name ?? '—' }}</dd></div>
        <div><dt class="ad-muted" style="font-size:.78rem">Date</dt><dd>{{ $order->created_at->format('d M Y, H:i') }}</dd></div>
        <div><dt class="ad-muted" style="font-size:.78rem">Customer Account</dt><dd>{{ $order->user?->name ?? 'Guest' }}</dd></div>
      </dl>
    </div>

    <div class="ad-card" style="margin-top:1.2rem">
      <h3 class="ad-card-title">Update Status</h3>
      <form method="POST" action="{{ route('admin.school-uniform-orders.status', $order) }}">
        @csrf @method('PATCH')
        <select class="ad-input" name="status">
          @foreach(['pending','processing','shipped','delivered','cancelled'] as $st)
            <option value="{{ $st }}" {{ $order->status === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
        <button class="ad-btn primary" style="margin-top:.6rem;width:100%">Update Status</button>
      </form>
    </div>
  </div>
</div>
@endsection
