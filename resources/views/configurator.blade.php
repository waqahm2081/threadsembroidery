@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero" style="padding-block:clamp(28px,4vw,44px)"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Live Customiser</span></div>
    <div class="flex justify-between items-center wrap gap-2">
      <div><span class="h-eyebrow">Design It. See It Live.</span><h1 class="mt-2 display-2--sm">Product Customiser</h1></div>
      <div class="flex gap-1 wrap">
        <span class="pill"><i class="bi bi-shield-check text-brand"></i> Free artwork setup</span>
        <span class="pill"><i class="bi bi-clock text-brand"></i> 10–14 day turnaround</span>
      </div>
    </div>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div class="cfg">
      <div class="cfg-stage-wrap">
        <div class="cfg-stage" id="cfgStage"></div>
        <p class="help text-center mt-2"><i class="bi bi-info-circle"></i> Logo sizing is set by our team for a consistent, professional finish — you choose the placement, we perfect the size.</p>
      </div>

      <div class="cfg-panel">
        <div class="cfg-prod-head">
          <img id="cfgHeadImg" src="" alt="">
          <div>
            <div class="brand" id="cfgHeadBrand"></div>
            <h2 id="cfgHeadTitle"></h2>
            <a class="cfg-change-prod" href="{{ route('catalogue') }}"><i class="bi bi-grid"></i> Browse catalogue</a>
          </div>
        </div>

        <div class="cfg-step" id="stepColour">
          <button class="cfg-step-head"><span class="n">1</span><span class="lbl">Colour &amp; Fabric <span class="sum" id="sumColour"></span></span><i class="bi bi-chevron-down chev"></i></button>
          <div class="cfg-step-body"><div class="cfg-step-inner">
            <p class="help" style="margin-bottom:.7rem">Selected colour: <b id="cfgColourName" style="color:var(--text)"></b></p>
            <div class="cfg-swatches" id="cfgColours"></div>
            <p class="help mt-2"><i class="bi bi-droplet"></i> Full fabric range available — ask us if you need a specific material or shade.</p>
          </div></div>
        </div>

        <div class="cfg-step" id="stepMethod">
          <button class="cfg-step-head"><span class="n">2</span><span class="lbl">Branding Method <span class="sum" id="sumMethod"></span></span><i class="bi bi-chevron-down chev"></i></button>
          <div class="cfg-step-body"><div class="cfg-step-inner">
            <div class="seg" id="cfgMethod">
              <button class="active" data-m="embroidery"><i class="bi bi-vector-pen"></i> Embroidery</button>
              <button data-m="print"><i class="bi bi-printer"></i> DTF / Print</button>
            </div>
            <p class="help mt-2">Embroidery for a premium stitched finish; DTF print for vibrant, full-colour designs.</p>
          </div></div>
        </div>

        <div class="cfg-step" id="stepPlace">
          <button class="cfg-step-head"><span class="n">3</span><span class="lbl">Placements &amp; Sizes <span class="sum" id="sumPlace"></span></span><i class="bi bi-chevron-down chev"></i></button>
          <div class="cfg-step-body"><div class="cfg-step-inner">
            <p class="help" style="margin-bottom:.6rem">Tick the positions you want. Sizes are set options for a consistent, professional result.</p>
            <div id="cfgPlacements"></div>
          </div></div>
        </div>

        <div class="cfg-step" id="stepLogo">
          <button class="cfg-step-head"><span class="n">4</span><span class="lbl">Upload Logo <span class="sum" id="sumLogo"></span></span><i class="bi bi-chevron-down chev"></i></button>
          <div class="cfg-step-body"><div class="cfg-step-inner">
            <div class="upload-zone" id="cfgUpload">
              <i class="bi bi-cloud-arrow-up"></i>
              <div><b>Drop your logo here</b> or click to browse</div>
              <div class="help" style="margin-top:.4rem">PNG, JPG, SVG or PDF — free artwork setup included</div>
            </div>
            <input type="file" id="cfgFile" accept=".png,.jpg,.jpeg,.svg,.pdf" hidden>
            <div id="cfgLogoPreview"></div>
          </div></div>
        </div>

        <div class="cfg-step" id="stepText">
          <button class="cfg-step-head"><span class="n">5</span><span class="lbl">Text Personalisation <span class="sum muted">Optional</span></span><i class="bi bi-chevron-down chev"></i></button>
          <div class="cfg-step-body"><div class="cfg-step-inner">
            <p class="help" style="margin-bottom:.7rem">Add names or numbers — perfect for teams and squads.</p>
            <div id="cfgTexts"></div>
          </div></div>
        </div>

        <div class="cfg-step" id="stepSizes">
          <button class="cfg-step-head"><span class="n">6</span><span class="lbl">Sizes &amp; Quantity <span class="sum" id="sumQty"></span></span><i class="bi bi-chevron-down chev"></i></button>
          <div class="cfg-step-body"><div class="cfg-step-inner">
            <p class="help" style="margin-bottom:.7rem">Enter quantities per size — mix sizes freely for team &amp; bulk orders.</p>
            <div class="size-grid" id="cfgSizes"></div>
            <div class="qtybar"><span class="muted">Total quantity</span><b id="cfgQtyTotal">0</b></div>
          </div></div>
        </div>

        <div class="cfg-summary" id="cfgSummary"></div>
      </div>
    </div>
  </div></section>
</main>
@endsection
