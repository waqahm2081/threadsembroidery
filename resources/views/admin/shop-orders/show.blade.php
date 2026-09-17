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
              $placements = $item->design_data['decoration_placements'] ?? [];
              if (! is_array($placements) || $placements === []) {
                  $single = $item->design_data['decoration_placement'] ?? null;
                  $placements = $single ? [$single] : [];
              }
              $sizeLabel = $item->size_breakdown
                ? collect($item->size_breakdown)->map(fn ($r) => ($r['size'] ?? '').' × '.($r['qty'] ?? 0))->implode(', ')
                : ($item->size ?: '—');
              $previewUrls = \App\Support\DesignPreview::resolveAllUrls(
                  $item->design_file_path,
                  $item->design_assets,
                  $item->design_data
              );
              $previewByPlacement = collect($item->design_data['preview_by_placement'] ?? [])
                  ->filter(fn ($p) => is_string($p) && $p !== '')
                  ->mapWithKeys(fn ($path, $key) => [$key => asset('storage/'.$path)])
                  ->all();
              $hasPrintFile = collect($item->design_assets ?? [])
                ->contains(fn ($p) => is_string($p) && str_contains(basename($p), 'print-'))
                || ! empty($item->design_data['print_file_path']);
              $hasDesign = !empty($item->design_data) || !empty($item->design_file_path);
              $designPreviewPayload = [
                  'title' => $item->product_title,
                  'colour_hex' => $item->colour_hex ?? ($item->design_data['colour']['hex'] ?? '#ffffff'),
                  'colour_name' => $item->colour_name ?? ($item->design_data['colour']['name'] ?? ''),
                  'customizer_image' => $item->design_data['customizer_image'] ?? null,
                  'customizer_image_back' => $item->design_data['customizer_image_back'] ?? null,
                  'customizer_image_sleeve' => $item->design_data['customizer_image_sleeve'] ?? null,
                  'size_breakdown' => $item->size_breakdown,
                  'quantity' => $item->quantity,
                  'design_data' => $item->design_data,
                  'storage_base' => asset('storage'),
                  'preview_url' => $previewUrls[0] ?? null,
                  'preview_urls' => $previewUrls,
                  'preview_by_placement' => $previewByPlacement,
              ];
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
                  <div class="ad-shop-design-preview-gallery" data-admin-design='@json($designPreviewPayload)'>
                    @if(count($previewByPlacement))
                      @foreach($previewByPlacement as $placeKey => $url)
                        <figure class="ad-shop-design-preview">
                          <figcaption>{{ str_replace('_', ' ', ucwords($placeKey, '_')) }}</figcaption>
                          <img src="{{ $url }}" alt="{{ $placeKey }} design for {{ $item->product_title }}">
                        </figure>
                      @endforeach
                    @elseif(count($previewUrls) > 1)
                      @foreach($previewUrls as $i => $url)
                        <figure class="ad-shop-design-preview">
                          <figcaption>Position {{ $i + 1 }}</figcaption>
                          <img src="{{ $url }}" alt="Saved design {{ $i + 1 }} for {{ $item->product_title }}">
                        </figure>
                      @endforeach
                    @elseif(count($previewUrls) === 1 && count($placements) <= 1)
                      <div class="ad-shop-design-preview">
                        <img src="{{ $previewUrls[0] }}" alt="Saved design for {{ $item->product_title }}">
                      </div>
                    @else
                      <div class="ad-shop-design-live" data-live-design></div>
                      @unless(count($previewUrls))
                        <div class="ad-shop-design-empty ad-shop-design-empty--fallback">
                          <i class="bi bi-palette"></i>
                          <span>Design data saved — open positions below</span>
                        </div>
                      @endunless
                    @endif
                  </div>
                  <div class="ad-shop-design-meta">
                    <h4>Saved Design</h4>
                    @if($placement)
                      <p>Placement: {{ $placement }}</p>
                    @endif
                    @if(count($placements) > 1)
                      <p>{{ count($placements) }} positions</p>
                    @elseif(!empty($item->design_data['view']))
                      <p>View: {{ ucfirst($item->design_data['view']) }}</p>
                    @endif
                    @php $layerCount = count($item->design_data['layers'] ?? []); @endphp
                    @if($layerCount)
                      <p>{{ $layerCount }} design element{{ $layerCount === 1 ? '' : 's' }}</p>
                    @endif
                    <div class="ad-shop-design-actions">
                      @if(count($previewUrls))
                        <a class="ad-btn sm" href="{{ $previewUrls[0] }}" target="_blank" rel="noopener">
                          <i class="bi bi-eye"></i> View
                        </a>
                      @endif
                      <a class="ad-btn sm" href="{{ route('admin.shop-orders.design.download', [$order, $item]) }}?type=preview">
                        <i class="bi bi-download"></i> Download Design
                      </a>
                      @if($hasPrintFile)
                        <a class="ad-btn sm" href="{{ route('admin.shop-orders.design.download', [$order, $item]) }}?type=print">
                          <i class="bi bi-printer"></i> Download Print Plate
                        </a>
                      @endif
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
            <span class="ad-badge {{ \App\Support\OrderFulfillmentStatus::badgeClass($order->status) }}">
              {{ \App\Support\OrderFulfillmentStatus::label($order->status) }}
            </span>
          </dd>
        </div>
        <div>
          <dt class="ad-muted" style="font-size:.78rem">Tracking</dt>
          <dd>{{ $order->tracking_number ?: '—' }}</dd>
        </div>
      </dl>
    </div>

    <div class="ad-card" style="margin-top:1.2rem">
      <h3 class="ad-card-title">Update Status</h3>
      <form method="POST" action="{{ route('admin.shop-orders.status', $order) }}" class="js-order-status-form">
        @csrf @method('PATCH')
        <label class="ad-label" for="shopOrderStatus">Status</label>
        <select class="ad-input js-order-status" id="shopOrderStatus" name="status">
          @foreach($statuses as $value => $label)
            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
          @endforeach
        </select>
        <div class="js-tracking-wrap" style="margin-top:.75rem;{{ $order->status === 'dispatched' ? '' : 'display:none' }}">
          <label class="ad-label" for="shopTrackingNumber">Tracking number</label>
          <input class="ad-input" id="shopTrackingNumber" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Carrier tracking / reference">
          @error('tracking_number')<p class="ad-error">{{ $message }}</p>@enderror
        </div>
        <p class="ad-field-hint" style="margin-top:.55rem">Saving will email the customer with this status update.</p>
        <button class="ad-btn primary" style="margin-top:.6rem;width:100%">Save &amp; notify customer</button>
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
  grid-template-columns:minmax(180px, 1fr) 1fr;
  gap:1.1rem;
  align-items:start;
  padding:.55rem 0;
  color:var(--ad-text);
}
.ad-shop-design-preview-gallery{
  display:flex;
  flex-wrap:wrap;
  gap:.75rem;
}
.ad-shop-design-preview{
  width:180px;
  min-height:140px;
  border-radius:12px;
  overflow:hidden;
  background:#0f1115;
  border:1px solid var(--ad-line);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
  margin:0;
}
.ad-shop-design-preview figcaption,
.ad-shop-design-live-label{
  width:100%;
  padding:.35rem .5rem 0;
  font-size:.72rem;
  font-weight:700;
  color:var(--ad-muted);
  text-align:center;
  text-transform:capitalize;
}
.ad-shop-design-preview img{
  width:100%;
  height:auto;
  max-height:200px;
  object-fit:contain;
  display:block;
}
.ad-shop-design-live{
  display:flex;
  flex-wrap:wrap;
  gap:.75rem;
}
.ad-shop-design-live-panel{
  width:180px;
}
.ad-shop-design-live-canvas{
  position:relative;
  width:180px;
  aspect-ratio:4/5;
  background:#0f1115;
  border:1px solid var(--ad-line);
  border-radius:12px;
  overflow:hidden;
}
.ad-shop-design-live-canvas img{
  position:absolute;
  inset:0;
  width:100%;
  height:100%;
  object-fit:contain;
}
.ad-shop-design-live-layers{
  position:absolute;
  inset:0;
}
.ad-shop-design-live-layers .cdm-layer-item{
  position:absolute;
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
  box-sizing:border-box;
}
.ad-shop-design-live-layers .cdm-layer-item img{
  position:static;
  width:100%;
  height:100%;
  object-fit:contain;
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
.ad-shop-design-empty--fallback{display:none}
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

@push('scripts')
<script src="{{ asset('assets/js/design-print-area.js') }}?v=1.7"></script>
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

  function placementKeys(data, preview) {
    const fromData = Array.isArray(data.decoration_placements) ? data.decoration_placements.filter(Boolean) : [];
    if (fromData.length) return [...new Set(fromData)];
    const map = preview.preview_by_placement || {};
    const fromMap = Object.keys(map);
    if (fromMap.length) return fromMap;
    if (data.decoration_placement) return [data.decoration_placement];
    return [...new Set((data.layers || []).map(l => l.placement || l.view || 'front'))];
  }

  function labelFor(key, data) {
    const pa = window.TEDesignPrintArea;
    return pa?.getPlacement?.(key)?.label
      || String(key || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
  }

  function garmentFor(preview, data, view) {
    const front = preview.customizer_image || data.customizer_image || '';
    const back = preview.customizer_image_back || data.customizer_image_back || front;
    const sleeve = preview.customizer_image_sleeve || data.customizer_image_sleeve || front;
    if (view === 'back') return back;
    if (view === 'sleeve') return sleeve;
    return front;
  }

  function layersFor(data, key) {
    const pa = window.TEDesignPrintArea;
    const placement = pa?.getPlacement?.(key);
    const view = placement?.view || key;
    return (data.layers || []).filter(l => l.placement ? l.placement === key : (l.view || 'front') === view);
  }

  document.querySelectorAll('[data-live-design]').forEach((host) => {
    const wrap = host.closest('[data-admin-design]');
    if (!wrap) return;
    let preview = {};
    try { preview = JSON.parse(wrap.getAttribute('data-admin-design') || '{}'); } catch { return; }
    const data = preview.design_data || {};
    const keys = placementKeys(data, preview);
    if (!keys.length) return;

    host.className = 'ad-shop-design-live';
    const empty = wrap.querySelector('.ad-shop-design-empty--fallback');
    if (empty) empty.style.display = 'none';

    keys.forEach((key) => {
      const pa = window.TEDesignPrintArea;
      const placement = pa?.getPlacement?.(key);
      const view = placement?.view || (key.includes('back') ? 'back' : key === 'sleeve' ? 'sleeve' : 'front');
      const panel = document.createElement('div');
      panel.className = 'ad-shop-design-live-panel';
      const label = document.createElement('div');
      label.className = 'ad-shop-design-live-label';
      label.textContent = labelFor(key, data);
      panel.appendChild(label);

      const canvas = document.createElement('div');
      canvas.className = 'ad-shop-design-live-canvas';
      const img = document.createElement('img');
      img.src = garmentFor(preview, data, view);
      img.alt = labelFor(key, data);
      canvas.appendChild(img);

      const layersEl = document.createElement('div');
      layersEl.className = 'ad-shop-design-live-layers';
      if (pa && placement?.printArea) pa.applyClip(layersEl, placement.printArea);
      layersFor(data, key).forEach((layer) => {
        const el = document.createElement('div');
        el.className = 'cdm-layer-item';
        el.style.left = layer.x + '%';
        el.style.top = layer.y + '%';
        el.style.width = layer.w + '%';
        el.style.height = layer.h + '%';
        if (layer.type === 'image') {
          const layerImg = document.createElement('img');
          layerImg.src = layer.src || (layer.asset_path ? (preview.storage_base + '/' + layer.asset_path) : '');
          el.appendChild(layerImg);
        } else {
          el.textContent = layer.text || '';
          el.style.color = layer.colour || '#fff';
          el.style.fontSize = (layer.fontSize || 14) + 'px';
          el.style.fontFamily = layer.font || 'sans-serif';
          el.style.fontWeight = layer.fontWeight || '700';
          el.style.justifyContent = 'center';
        }
        layersEl.appendChild(el);
      });
      canvas.appendChild(layersEl);
      panel.appendChild(canvas);
      host.appendChild(panel);
    });
  });
})();
</script>
@endpush
