@extends('admin.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link rel="stylesheet" href="{{ asset('css/configurator.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-customizer.css') }}">
@endpush

@section('content')
<div class="ad-cfg">
  <div class="cfg">
    <div class="cfg-stage-wrap">
      <div class="cfg-stage" id="cfgStage"></div>
      <p class="help text-center mt-2"><i class="bi bi-info-circle"></i> Logo sizing is set for a consistent professional finish — choose placement, preview live.</p>
    </div>

    <div class="cfg-panel">
      <div class="cfg-prod-head">
        <img id="cfgHeadImg" src="" alt="">
        <div style="flex:1;min-width:0">
          <div class="brand" id="cfgHeadBrand"></div>
          <h2 id="cfgHeadTitle"></h2>
          <label class="ad-label" for="cfgProductSelect" style="margin-top:.65rem">Choose product</label>
          <select class="ad-input" id="cfgProductSelect"></select>
        </div>
      </div>

      <div class="cfg-step" id="stepColour">
        <button type="button" class="cfg-step-head"><span class="n">1</span><span class="lbl">Colour &amp; Fabric <span class="sum" id="sumColour"></span></span><i class="bi bi-chevron-down chev"></i></button>
        <div class="cfg-step-body"><div class="cfg-step-inner">
          <p class="help" style="margin-bottom:.7rem">Selected colour: <b id="cfgColourName" style="color:var(--text)"></b></p>
          <div class="cfg-swatches" id="cfgColours"></div>
        </div></div>
      </div>

      <div class="cfg-step" id="stepMethod">
        <button type="button" class="cfg-step-head"><span class="n">2</span><span class="lbl">Branding Method <span class="sum" id="sumMethod"></span></span><i class="bi bi-chevron-down chev"></i></button>
        <div class="cfg-step-body"><div class="cfg-step-inner">
          <div class="seg" id="cfgMethod">
            <button type="button" class="active" data-m="embroidery"><i class="bi bi-vector-pen"></i> Embroidery</button>
            <button type="button" data-m="print"><i class="bi bi-printer"></i> DTF / Print</button>
          </div>
          <p class="help mt-2">Embroidery for a premium stitched finish; DTF print for vibrant, full-colour designs.</p>
        </div></div>
      </div>

      <div class="cfg-step" id="stepPlace">
        <button type="button" class="cfg-step-head"><span class="n">3</span><span class="lbl">Placements &amp; Sizes <span class="sum" id="sumPlace"></span></span><i class="bi bi-chevron-down chev"></i></button>
        <div class="cfg-step-body"><div class="cfg-step-inner">
          <p class="help" style="margin-bottom:.6rem">Tick the positions you want. Sizes are set options for a consistent result.</p>
          <div id="cfgPlacements"></div>
        </div></div>
      </div>

      <div class="cfg-step" id="stepLogo">
        <button type="button" class="cfg-step-head"><span class="n">4</span><span class="lbl">Upload Logo <span class="sum" id="sumLogo"></span></span><i class="bi bi-chevron-down chev"></i></button>
        <div class="cfg-step-body"><div class="cfg-step-inner">
          <div class="upload-zone" id="cfgUpload">
            <i class="bi bi-cloud-arrow-up"></i>
            <div><b>Drop your logo here</b> or click to browse</div>
            <div class="help" style="margin-top:.4rem">PNG, JPG, SVG or PDF</div>
          </div>
          <input type="file" id="cfgFile" accept=".png,.jpg,.jpeg,.svg,.pdf" hidden>
          <div id="cfgLogoPreview"></div>
        </div></div>
      </div>

      <div class="cfg-step" id="stepText">
        <button type="button" class="cfg-step-head"><span class="n">5</span><span class="lbl">Text Personalisation <span class="sum muted">Optional</span></span><i class="bi bi-chevron-down chev"></i></button>
        <div class="cfg-step-body"><div class="cfg-step-inner">
          <p class="help" style="margin-bottom:.7rem">Add names or numbers — perfect for teams and squads.</p>
          <div id="cfgTexts"></div>
        </div></div>
      </div>

      <div class="cfg-step" id="stepSizes">
        <button type="button" class="cfg-step-head"><span class="n">6</span><span class="lbl">Sizes &amp; Quantity <span class="sum" id="sumQty"></span></span><i class="bi bi-chevron-down chev"></i></button>
        <div class="cfg-step-body"><div class="cfg-step-inner">
          <p class="help" style="margin-bottom:.7rem">Enter quantities per size — mix sizes freely.</p>
          <div class="size-grid" id="cfgSizes"></div>
          <div class="qtybar"><span class="muted">Total quantity</span><b id="cfgQtyTotal">0</b></div>
        </div></div>
      </div>

      <div class="cfg-summary" id="cfgSummary"></div>
    </div>
  </div>
</div>

<div class="modal-scrim" id="specModal">
  <div class="modal-card">
    <div class="modal-head"><h3 class="h-3">Order Specification</h3><button type="button" class="icon-btn" id="specClose"><i class="bi bi-x-lg"></i></button></div>
    <div style="padding:1.4rem" id="specBody"></div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="{{ asset('js/data.js') }}"></script>
<script>
  window.THREADS = window.THREADS || {};
  THREADS.base = @json(rtrim(asset('/'), '/') . '/');
  THREADS.ADMIN_QUOTE_STORE = @json(route('admin.product-quotes.store'));
  if (THREADS.CONFIG) {
    THREADS.CONFIG.placements = @json($placements);
  }
  // Admin customizer: shirts + hoodies only
  THREADS.PRODUCTS = (THREADS.PRODUCTS || []).filter(function (p) {
    var type = String(p.type || '').toLowerCase();
    var cat = String(p.category_slug || '').toLowerCase();
    if (['hi-vis', 'workwear', 'schoolwear', 'corporatewear', 'sports'].includes(cat)) return false;
    return type === 't-shirt' || type === 'polo' || type === 'shirt' || type === 'hood';
  });
</script>
<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/garment-templates.js') }}"></script>
<script src="{{ asset('js/configurator.js') }}"></script>
@endpush
