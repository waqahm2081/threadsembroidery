@extends('layouts.app')

@section('content')
<div class="artwork-studio">

<section class="hero"><div class="container">
  <span class="eyebrow">Free artwork setup</span>
  <h1>Turn your logo into<br>production-ready artwork.</h1>
  <p>Send us whatever you have, even a rough phone photo or a low quality file. We strip the background, reduce your design to solid thread colours, redraw it cleanly and match every colour to our thread chart, ready to stitch or print. It is free and it takes seconds.</p>
  <div class="stats">
    <span class="pill brand"><i class="bi bi-eraser"></i> Background removal</span>
    <span class="pill brand"><i class="bi bi-palette"></i> Thread-colour reduction</span>
    <span class="pill brand"><i class="bi bi-vector-pen"></i> Vector tracing</span>
    <span class="pill brand"><i class="bi bi-upc-scan"></i> Thread chart matching</span>
  </div>
</div></section>

{{-- Two doors --}}
<div class="container">
  <div class="modes" role="tablist">
    <button class="mode-card active" data-mode="refine" role="tab">
      <span class="mc-ic"><i class="bi bi-magic"></i></span>
      <span class="mc-txt">
        <b>I have my artwork</b>
        <span>Upload your logo and we will clean it up ready for stitching or print.</span>
      </span>
      <span class="mc-tag free">Free</span>
    </button>
    <button class="mode-card" data-mode="design" role="tab">
      <span class="mc-ic"><i class="bi bi-pencil-square"></i></span>
      <span class="mc-txt">
        <b>I need artwork designed</b>
        <span>No logo yet? Tell us what you need and our team will create it for you.</span>
      </span>
      <span class="mc-tag paid" id="modePriceTag">From £45</span>
    </button>
  </div>
</div>

<main class="container work" id="refineView">

  <aside class="panel sticky">
    <div class="panel-hd"><h3>1 &middot; Artwork</h3></div>
    <div class="panel-bd">
      <div class="drop" id="drop">
        <div class="ic"><i class="bi bi-cloud-arrow-up"></i></div>
        <b>Drop a logo here</b>
        <small>PNG, JPG or SVG &middot; or click to browse</small>
      </div>
      <input type="file" id="file" accept=".png,.jpg,.jpeg,.svg,.webp" hidden>

      <span class="samples-lbl">Or try one of these:</span>
      <div class="samples">
        <button type="button" data-sample="{{ asset('assets/artwork-studio/samples/riverside-fc.png') }}" title="Riverside FC"><img src="{{ asset('assets/artwork-studio/samples/riverside-fc.png') }}" alt="Riverside FC sample"></button>
        <button type="button" data-sample="{{ asset('assets/artwork-studio/samples/thornton-building.png') }}" title="Thornton Building Services"><img src="{{ asset('assets/artwork-studio/samples/thornton-building.png') }}" alt="Thornton sample"></button>
        <button type="button" data-sample="{{ asset('assets/artwork-studio/samples/greenfield-academy.png') }}" title="Greenfield Academy"><img src="{{ asset('assets/artwork-studio/samples/greenfield-academy.png') }}" alt="Greenfield sample"></button>
      </div>

      <div class="panel-hd" style="margin:1.4rem -1.2rem 0;border-top:1px solid var(--line)"><h3>2 &middot; Settings</h3></div>

      <div class="ctrl">
        <div class="ctrl-hd"><label for="colours">Thread colours</label><span class="val" id="coloursVal">4</span></div>
        <input type="range" id="colours" min="2" max="8" value="4">
        <div class="hint">Embroidery runs one thread per colour. Fewer colours means a cleaner, cheaper stitch-out.</div>
      </div>

      <div class="ctrl">
        <div class="ctrl-hd"><label for="tolerance">Background sensitivity</label><span class="val" id="tolVal">56</span></div>
        <input type="range" id="tolerance" min="10" max="140" value="56">
        <div class="hint">Higher removes more of a busy or shadowed background. Lower protects pale detail in the logo.</div>
      </div>

      <div class="ctrl">
        <div class="ctrl-hd"><label for="detail">Edge smoothing</label><span class="val" id="detailVal">Balanced</span></div>
        <input type="range" id="detail" min="3" max="22" value="12">
        <div class="hint">Smoother edges stitch better. Sharper keeps fine detail and small text.</div>
      </div>

      <button type="button" class="btn" id="runBtn" style="margin-top:1.4rem" disabled><i class="bi bi-magic"></i> Prepare Artwork</button>
      <button type="button" class="btn ghost sm" id="resetBtn" style="margin-top:.6rem"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
    </div>
  </aside>

  <section class="stage-wrap">
    <div class="compare">
      <div class="face">
        <div class="cap"><span>Your artwork</span><span id="inMeta"></span></div>
        <div class="canvasbox" id="beforeBox">
          <div class="empty"><span class="ic"><i class="bi bi-image"></i></span>Drop a logo to begin</div>
        </div>
      </div>
      <div class="face">
        <div class="cap"><span class="b">Production ready</span><span id="outMeta"></span></div>
        <div class="canvasbox plain" id="afterBox">
          <div class="empty"><span class="ic"><i class="bi bi-vector-pen"></i></span>Vector output appears here</div>
          <div class="proc" id="proc">
            <div class="ring"></div>
            <div class="step" id="procStep">Working…</div>
            <div class="bar"><i id="procBar"></i></div>
            <div class="sub" id="procSub">Processing locally in your browser</div>
          </div>
        </div>
      </div>
    </div>

    <div class="resbar" id="resBar"></div>

    <div class="mock" id="mockWrap" hidden>
      <div class="panel">
        <div class="panel-hd"><h3>On the garment</h3><span class="pill" id="mockNote">Left chest preview</span></div>
        <div class="panel-bd">
          <div class="mock-grid" id="mockGrid"></div>
        </div>
      </div>
    </div>
  </section>

  <aside class="panel side-threads sticky">
    <div class="panel-hd"><h3>Thread breakdown</h3><span class="pill" id="threadCount">—</span></div>
    <div class="panel-bd" id="threadList">
      <div class="empty-note">Colours and their matching threads will be listed here once artwork is prepared.</div>
    </div>
    <div class="panel-hd" style="border-top:1px solid var(--line)"><h3>Export</h3></div>
    <div class="panel-bd">
      <button type="button" class="btn ghost sm" id="dlSvg" disabled style="margin-bottom:.5rem"><i class="bi bi-filetype-svg"></i> Download vector (SVG)</button>
      <button type="button" class="btn ghost sm" id="dlPng" disabled style="margin-bottom:.5rem"><i class="bi bi-filetype-png"></i> Download transparent PNG</button>
      <button type="button" class="btn ghost sm" id="dlSheet" disabled><i class="bi bi-clipboard-check"></i> Copy thread sheet</button>
      <div class="hint" style="margin-top:.7rem">These are the files we need to stitch or print your design properly, and they stay sharp at any size.</div>
    </div>
  </aside>
</main>

<section class="container brief-wrap" id="designView" hidden>
  <form class="panel" id="briefForm" novalidate>
    <div class="panel-hd"><h3>Artwork brief</h3><span class="pill">Takes about a minute</span></div>
    <div class="panel-bd">

      <p class="brief-intro">Tell us what you need and our team will design it for you. The more you give us here, the closer the first concepts will be.</p>

      <div class="f-row two">
        <div class="fld">
          <label for="bName">Business or club name <span class="req">*</span></label>
          <input id="bName" name="organisation" placeholder="e.g. Riverside FC">
          <span class="err">Please tell us the name</span>
        </div>
        <div class="fld">
          <label for="bWhat">What do you do? <span class="req">*</span></label>
          <input id="bWhat" name="what_you_do" placeholder="e.g. Sunday league football club">
          <span class="err">A short line is fine</span>
        </div>
      </div>

      <div class="fld">
        <label>What do you need? <span class="req">*</span></label>
        <div class="pkg-grid" id="pkgGrid"></div>
        <span class="err" id="pkgErr">Please choose one</span>
      </div>

      <div class="fld">
        <label>Where will it go?</label>
        <div class="chips" id="placeChips">
          <label><input type="checkbox" name="placement" value="Left chest"><span>Left chest</span></label>
          <label><input type="checkbox" name="placement" value="Back"><span>Back</span></label>
          <label><input type="checkbox" name="placement" value="Sleeve"><span>Sleeve</span></label>
          <label><input type="checkbox" name="placement" value="Cap"><span>Cap</span></label>
          <label><input type="checkbox" name="placement" value="Bag"><span>Bag</span></label>
        </div>
      </div>

      <div class="fld">
        <label>Style</label>
        <div class="chips" id="styleChips">
          <label><input type="checkbox" name="style" value="Modern"><span>Modern</span></label>
          <label><input type="checkbox" name="style" value="Traditional"><span>Traditional</span></label>
          <label><input type="checkbox" name="style" value="Bold"><span>Bold</span></label>
          <label><input type="checkbox" name="style" value="Minimal"><span>Minimal</span></label>
          <label><input type="checkbox" name="style" value="Sporty"><span>Sporty</span></label>
          <label><input type="checkbox" name="style" value="Vintage"><span>Vintage</span></label>
        </div>
      </div>

      <div class="f-row two">
        <div class="fld">
          <label for="bColours">Colours</label>
          <input id="bColours" name="colours" placeholder="e.g. navy and gold, or not sure yet">
        </div>
        <div class="fld">
          <label for="bDeadline">Needed by</label>
          <input id="bDeadline" name="deadline" type="date">
        </div>
      </div>

      <div class="fld">
        <label for="bNotes">Anything else we should know?</label>
        <textarea id="bNotes" name="notes" placeholder="Wording, initials, year established, anything you want included or avoided."></textarea>
      </div>

      <div class="fld">
        <label>Sketches or references <span class="opt">optional</span></label>
        <div class="drop sm" id="briefDrop">
          <i class="bi bi-paperclip"></i> <b>Attach files</b>
          <small>A rough sketch, a photo, or a logo you like the look of</small>
        </div>
        <input type="file" id="briefFiles" multiple hidden accept="image/*,.pdf">
        <div id="briefFileList"></div>
      </div>

      <div class="panel-hd" style="margin:1.6rem -1.2rem 1.2rem;border-top:1px solid var(--line);border-bottom:1px solid var(--line)"><h3>Your details</h3></div>

      <div class="f-row two">
        <div class="fld">
          <label for="bContact">Your name <span class="req">*</span></label>
          <input id="bContact" name="name" placeholder="Full name">
          <span class="err">Please add your name</span>
        </div>
        <div class="fld">
          <label for="bEmail">Email <span class="req">*</span></label>
          <input id="bEmail" name="email" type="email" placeholder="you@email.com">
          <span class="err">Please add a valid email</span>
        </div>
      </div>
      <div class="fld">
        <label for="bPhone">Phone <span class="opt">optional</span></label>
        <input id="bPhone" name="phone" type="tel" placeholder="07…">
      </div>
    </div>
  </form>

  <aside class="panel brief-side">
    <div class="panel-hd"><h3>Your brief</h3></div>
    <div class="panel-bd" id="briefSummary"></div>
  </aside>
</section>

<div class="modal-scrim" id="leadModal">
  <div class="modal-card">
    <div class="modal-hd">
      <h3>Where shall we send your files?</h3>
      <button type="button" class="icon-x" id="leadClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="modal-bd">
      <p class="lead-note">Your artwork is ready and there is no charge for it. Pop your details in and the files are yours, we will email a copy across too so you always have them.</p>
      <form id="leadForm" novalidate>
        <div class="f-row two">
          <div class="fld">
            <label for="lName">Your name <span class="req">*</span></label>
            <input id="lName" name="name" placeholder="Full name">
            <span class="err">Please add your name</span>
          </div>
          <div class="fld">
            <label for="lEmail">Email <span class="req">*</span></label>
            <input id="lEmail" name="email" type="email" placeholder="you@email.com">
            <span class="err">Please add a valid email</span>
          </div>
        </div>
        <div class="f-row two">
          <div class="fld">
            <label for="lCompany">Business or club <span class="opt">optional</span></label>
            <input id="lCompany" name="organisation" placeholder="e.g. Riverside FC">
          </div>
          <div class="fld">
            <label for="lPhone">Phone <span class="opt">optional</span></label>
            <input id="lPhone" name="phone" type="tel" placeholder="07…">
          </div>
        </div>
        <button class="btn" type="submit"><i class="bi bi-download"></i> Get my files</button>
      </form>
    </div>
  </div>
</div>

<section class="value"><div class="container">
  <div class="value-grid">
    <div class="vcard"><div class="ic"><i class="bi bi-gift"></i></div><h4>Free with every order</h4><p>Getting your logo ready costs you nothing. It is included whenever you order with us.</p></div>
    <div class="vcard"><div class="ic"><i class="bi bi-eye"></i></div><h4>See it before you order</h4><p>Check exactly how your logo will look, and on which garments, before you commit to anything.</p></div>
    <div class="vcard"><div class="ic"><i class="bi bi-vector-pen"></i></div><h4>Stitch and print ready</h4><p>You get proper vector files, so your logo stays crisp on a cap or right across a back.</p></div>
    <div class="vcard"><div class="ic"><i class="bi bi-shield-lock"></i></div><h4>Your artwork stays yours</h4><p>Everything happens here in your browser. Your files are not sent off to any other service.</p></div>
  </div>
  <p class="footnote">Thread colours are matched to our standard thread chart. How close the finished artwork comes to your original depends on the quality of the file you send, so the clearer the better.</p>
</div></section>

<div id="toasts"></div>

</div>
@endsection
