@extends('admin.layouts.app')

@section('content')
<div style="display:grid;grid-template-columns:1fr 300px;gap:1.4rem;align-items:start">
  <div>
    <div class="ad-card">
      <h3 class="ad-card-title">Order Items</h3>
      <table class="ad-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Colour / Size</th>
            <th>Qty</th>
            <th>Fees</th>
            <th>Line Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
            @php
              $placement = $item->design_data['decoration_placement_label'] ?? null;
              $sizeLabel = $item->size_breakdown
                ? collect($item->size_breakdown)->map(fn ($r) => ($r['size'] ?? '').' × '.($r['qty'] ?? 0))->implode(', ')
                : ($item->size ?: '—');
              $previewPath = collect($item->design_assets ?? [])
                ->first(fn ($p) => is_string($p) && str_contains(basename($p), 'preview-'))
                ?: ($item->design_file_path && ! str_contains(basename((string) $item->design_file_path), 'print-')
                    ? $item->design_file_path
                    : null);
              $previewUrl = $previewPath ? asset('storage/'.$previewPath) : null;
              $hasDesign = !empty($item->design_data) || !empty($item->design_file_path);
            @endphp
            <tr>
              <td>
                <b>{{ $item->product_title }}</b>
                <div class="ad-muted" style="font-size:.78rem">{{ $item->style_code }}</div>
                @if($placement)
                  <div class="ad-muted" style="font-size:.78rem;margin-top:.2rem"><i class="bi bi-bounding-box"></i> {{ $placement }}</div>
                @endif
              </td>
              <td>
                <div>{{ $item->colour_name ?: '—' }}</div>
                <div class="ad-muted" style="font-size:.78rem">{{ $sizeLabel }}</div>
              </td>
              <td>{{ $item->quantity }}</td>
              <td style="font-size:.82rem">
                @if($item->logo_setup_fee > 0)
                  <div>Logo setup £{{ number_format($item->logo_setup_fee, 2) }}</div>
                @endif
                @if($item->decoration_fee > 0)
                  <div>Decoration £{{ number_format($item->decoration_fee, 2) }}/unit</div>
                @endif
                @if($item->logo_setup_fee <= 0 && $item->decoration_fee <= 0)
                  —
                @endif
              </td>
              <td><b>£{{ number_format($item->line_total, 2) }}</b></td>
            </tr>
            @if($hasDesign)
            <tr>
              <td colspan="5" class="ad-shop-design-cell">
                <div class="ad-shop-design">
                  <div class="ad-shop-design-preview">
                    @if($previewUrl)
                      <img src="{{ $previewUrl }}" alt="Saved design for {{ $item->product_title }}">
                    @else
                      <div class="ad-shop-design-empty">
                        <i class="bi bi-palette"></i>
                        <span>Design data saved (no preview image)</span>
                      </div>
                    @endif
                  </div>
                  <div class="ad-shop-design-meta">
                    <h4>Saved Design</h4>
                    @if($placement)
                      <p>Placement: {{ $placement }}</p>
                    @endif
                    @if(!empty($item->design_data['view']))
                      <p>View: {{ ucfirst($item->design_data['view']) }}</p>
                    @endif
                    @php $layerCount = count($item->design_data['layers'] ?? []); @endphp
                    @if($layerCount)
                      <p>{{ $layerCount }} design element{{ $layerCount === 1 ? '' : 's' }}</p>
                    @endif
                    <div class="ad-shop-design-actions">
                      @if($previewUrl)
                        <a class="ad-btn sm" href="{{ $previewUrl }}" target="_blank" rel="noopener">
                          <i class="bi bi-eye"></i> View
                        </a>
                      @endif
                      <a class="ad-btn sm" href="{{ route('admin.shop-orders.design.download', [$order, $item]) }}">
                        <i class="bi bi-download"></i> Download Design
                      </a>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
            @endif
          @endforeach
        </tbody>
        <tfoot>
          <tr><td colspan="4" style="text-align:right;font-weight:600">Subtotal (ex. VAT)</td><td>£{{ number_format($order->subtotal, 2) }}</td></tr>
          <tr><td colspan="4" style="text-align:right;font-weight:600">VAT</td><td>£{{ number_format($order->vat_amount, 2) }}</td></tr>
          <tr><td colspan="4" style="text-align:right;font-weight:700;font-size:1rem">Total (inc. VAT)</td><td><b>£{{ number_format($order->total, 2) }}</b></td></tr>
        </tfoot>
      </table>
    </div>

    <div class="ad-card" style="margin-top:1.2rem">
      <h3 class="ad-card-title">Customer &amp; Shipping</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem 1.4rem">
        <div><span class="ad-muted" style="font-size:.78rem">Name</span><b style="display:block">{{ $order->name }}</b></div>
        <div><span class="ad-muted" style="font-size:.78rem">Email</span><b style="display:block">{{ $order->email }}</b></div>
        <div><span class="ad-muted" style="font-size:.78rem">Phone</span><b style="display:block">{{ $order->phone ?: '—' }}</b></div>
        <div><span class="ad-muted" style="font-size:.78rem">Company</span><b style="display:block">{{ $order->company_name ?: '—' }}</b></div>
        <div style="grid-column:1/-1">
          <span class="ad-muted" style="font-size:.78rem">Shipping Address</span>
          <b style="display:block">{{ implode(', ', array_filter([$order->shipping_address_line1, $order->shipping_city, $order->shipping_state, $order->shipping_zip])) }}</b>
        </div>
        <div style="grid-column:1/-1">
          <span class="ad-muted" style="font-size:.78rem">Billing</span>
          @if($order->billing_same_as_shipping)
            <b style="display:block">Same as shipping</b>
          @else
            <b style="display:block">{{ implode(', ', array_filter([$order->billing_company_name, $order->billing_address_line1, $order->billing_city, $order->billing_state, $order->billing_zip])) }}</b>
          @endif
        </div>
        @if($order->additional_instructions)
          <div style="grid-column:1/-1">
            <span class="ad-muted" style="font-size:.78rem">Instructions</span>
            <p style="margin:.3rem 0 0">{{ $order->additional_instructions }}</p>
          </div>
        @endif
      </div>
    </div>
  </div>

  <div>
    <div class="ad-card">
      <h3 class="ad-card-title">Details</h3>
      <dl style="display:grid;gap:.6rem">
        <div><dt class="ad-muted" style="font-size:.78rem">Order Number</dt><dd><b>{{ $order->order_number }}</b></dd></div>
        <div><dt class="ad-muted" style="font-size:.78rem">Date</dt><dd>{{ optional($order->created_at)->format('d M Y, H:i') }}</dd></div>
        <div>
          <dt class="ad-muted" style="font-size:.78rem">Customer Account</dt>
          <dd>
            @if($order->user)
              <a class="ad-link" href="{{ route('admin.users.edit', $order->user) }}">{{ $order->user->name }}</a>
            @else
              Guest
            @endif
          </dd>
        </div>
        <div>
          <dt class="ad-muted" style="font-size:.78rem">Status</dt>
          <dd>
            <span class="ad-badge {{ match($order->status){ 'delivered'=>'green','cancelled'=>'red','shipped','processing'=>'blue',default=>'yellow' } }}">
              {{ ucfirst($order->status) }}
            </span>
          </dd>
        </div>
      </dl>
    </div>

    <div class="ad-card" style="margin-top:1.2rem">
      <h3 class="ad-card-title">Update Status</h3>
      <form method="POST" action="{{ route('admin.shop-orders.status', $order) }}">
        @csrf @method('PATCH')
        <select class="ad-input" name="status">
          @foreach($statuses as $st)
            <option value="{{ $st }}" @selected($order->status === $st)>{{ ucfirst($st) }}</option>
          @endforeach
        </select>
        <button class="ad-btn primary" style="margin-top:.6rem;width:100%">Update Status</button>
      </form>
    </div>

    <div style="margin-top:1.2rem">
      <a href="{{ route('admin.shop-orders.index') }}" class="ad-btn ghost" style="width:100%;justify-content:center">
        <i class="bi bi-arrow-left"></i> Back to Shop Orders
      </a>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.ad-shop-design-cell{
  background:var(--ad-surface-2) !important;
  border-top:1px solid var(--ad-line);
}
.ad-shop-design{
  display:grid;
  grid-template-columns:180px 1fr;
  gap:1.1rem;
  align-items:center;
  padding:.55rem 0;
  color:var(--ad-text);
}
.ad-shop-design-preview{
  width:180px;
  min-height:140px;
  border-radius:12px;
  overflow:hidden;
  background:#0f1115;
  border:1px solid var(--ad-line);
  display:flex;
  align-items:center;
  justify-content:center;
}
.ad-shop-design-preview img{
  width:100%;
  height:auto;
  max-height:200px;
  object-fit:contain;
  display:block;
}
.ad-shop-design-empty{
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:.35rem;
  padding:1rem;
  color:var(--ad-muted);
  font-size:.78rem;
  text-align:center;
}
.ad-shop-design-empty i{font-size:1.4rem;color:var(--ad-brand-hi)}
.ad-shop-design-meta h4{
  margin:0 0 .45rem;
  color:var(--ad-text);
  font-size:1rem;
  font-weight:700;
}
.ad-shop-design-meta p{
  margin:0 0 .35rem;
  color:var(--ad-muted);
  font-size:.86rem;
}
.ad-shop-design-actions{
  display:flex;
  flex-wrap:wrap;
  gap:.5rem;
  margin-top:.75rem;
}
.ad-shop-design-actions .ad-btn{
  color:#fff !important;
  background:var(--ad-brand);
  border:none;
}
.ad-shop-design-actions .ad-btn:hover{
  background:var(--ad-brand-hi);
  color:#fff !important;
}
@media (max-width:720px){
  .ad-shop-design{grid-template-columns:1fr}
  .ad-shop-design-preview{width:100%}
}
</style>
@endpush
