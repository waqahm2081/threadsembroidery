@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Branding Enquiry</span></div>
    <span class="h-eyebrow">Let's Talk</span>
    <h1 class="mt-2">Branding Enquiry</h1>
    <p class="lead">For bulk orders, complex jobs, or branding garments you've supplied yourself — tell us what you need and we'll come back with a tailored quote.</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div class="enquiry-layout">
      <aside class="enquiry-aside" data-reveal>
        <div class="enquiry-aside-head">
          <span class="h-eyebrow">Why enquire</span>
          <h2 class="h-3 mt-2">We'll quote it properly</h2>
          <p class="muted" style="margin-top:.6rem;font-size:.92rem">Bulk runs, BYO garments and complex branding — send the details and we'll come back with a clear quote.</p>
        </div>
        <div class="contact-cards">
          <div class="contact-card"><span class="ic"><i class="bi bi-box-seam"></i></span><div><b>Bring Your Own Garment</b><span>Send us items you've purchased — we'll embroider or print them. Min. 5 garments.</span></div></div>
          <div class="contact-card"><span class="ic"><i class="bi bi-stack"></i></span><div><b>Bulk &amp; Team Orders</b><span>Large runs, mixed sizes, multiple placements — no problem.</span></div></div>
          <div class="contact-card"><span class="ic"><i class="bi bi-clock-history"></i></span><div><b>10–14 Working Days</b><span>Clear turnaround from order confirmation.</span></div></div>
          <div class="contact-card"><span class="ic"><i class="bi bi-telephone"></i></span><div><b>Prefer to talk?</b><span><a href="tel:+447916916369">+44 7916 916369</a></span></div></div>
        </div>
        <div class="enquiry-aside-foot">
          <ul class="enquiry-assures">
            <li><i class="bi bi-check-circle-fill"></i> Free artwork setup on every order</li>
            <li><i class="bi bi-check-circle-fill"></i> Free UK delivery over £150</li>
            <li><i class="bi bi-check-circle-fill"></i> 100% quality guarantee</li>
          </ul>
          <a class="enquiry-mail" href="mailto:customerservice@threadsembroideryprint.co.uk"><i class="bi bi-envelope-fill"></i> customerservice@threadsembroideryprint.co.uk</a>
        </div>
      </aside>
      <div class="form-card enquiry-form" data-reveal data-delay="1">
        <div class="enquiry-form-head">
          <h3 class="h-3">Send your enquiry</h3>
          <p class="muted" style="margin-top:.4rem;font-size:.9rem">Fill in as much as you can — the more detail, the faster we can quote.</p>
        </div>
        @php $profile = auth()->user(); @endphp
        <form data-enquiry="branding" enctype="multipart/form-data" novalidate>
          <div class="field-group two">
            <div><label class="lbl">Full Name <span class="req">*</span></label><input class="input" name="name" required placeholder="Jane Smith" value="{{ old('name', $profile?->name) }}" autocomplete="name"><div class="err-msg">Please enter your name.</div></div>
            <div><label class="lbl">Company / Organisation</label><input class="input" name="company" placeholder="e.g. Greenfield Academy" value="{{ old('company', $profile?->company_name) }}" autocomplete="organization"></div>
          </div>
          <div class="field-group two">
            <div><label class="lbl">Email <span class="req">*</span></label><input class="input" type="email" name="email" required placeholder="you@company.co.uk" value="{{ old('email', $profile?->email) }}" autocomplete="email"><div class="err-msg">Enter a valid email address.</div></div>
            <div><label class="lbl">Phone</label><input class="input" type="tel" name="phone" placeholder="07…" value="{{ old('phone', $profile?->phone) }}" autocomplete="tel"><div class="err-msg">Enter a valid phone number.</div></div>
          </div>
          <div class="field-group">
            <label class="lbl">Enquiry Type <span class="req">*</span></label>
            <select class="select" name="enquiry_type" required>
              <option value="">Select an option…</option>
              <option>Bring your own garment (embroider/print my items)</option>
              <option>Bulk / team order</option>
              <option>Bundle deal enquiry</option>
              <option>Schoolwear / uniform programme</option>
              <option>General question</option>
            </select>
            <div class="err-msg">Please choose an enquiry type.</div>
          </div>
          <div class="field-group two">
            <div><label class="lbl">Approx. Quantity</label><input class="input" name="quantity" placeholder="e.g. 25"></div>
            <div><label class="lbl">Branding Method</label><select class="select" name="method"><option value="">No preference</option><option>Embroidery</option><option>DTF / Print</option><option>Both</option></select></div>
          </div>
          <div class="field-group">
            <label class="lbl">Placements Needed</label>
            <div class="chip-select">
              <label><input type="checkbox" name="placement[]" value="Left Chest"><span class="chip"><i class="bi bi-check"></i> Left Chest</span></label>
              <label><input type="checkbox" name="placement[]" value="Back Print"><span class="chip"><i class="bi bi-check"></i> Back Print</span></label>
              <label><input type="checkbox" name="placement[]" value="Back Embroidery"><span class="chip"><i class="bi bi-check"></i> Back Embroidery</span></label>
              <label><input type="checkbox" name="placement[]" value="Sleeve"><span class="chip"><i class="bi bi-check"></i> Sleeve</span></label>
            </div>
          </div>
          <div class="field-group">
            <label class="lbl">Tell us about your project <span class="req">*</span></label>
            <textarea class="textarea" name="message" required placeholder="Garments, colours, sizes, logo details, deadline…"></textarea>
            <div class="err-msg">Please add a few details so we can quote accurately.</div>
          </div>
          <div class="field-group">
            <label class="lbl">Upload Logo / Reference (optional)</label>
            <div class="upload-zone"><i class="bi bi-cloud-arrow-up"></i><div><b>Drop files</b> or click to browse</div><div class="help" style="margin-top:.3rem">PNG, JPG, PDF, SVG</div></div>
            <input type="file" name="files[]" accept=".png,.jpg,.jpeg,.svg,.pdf" multiple hidden>
            <div data-file-list></div>
          </div>
          <button class="btn btn--block btn--lg" type="submit"><i class="bi bi-send"></i> Send Enquiry</button>
          <p class="help mt-2" style="text-align:center"><i class="bi bi-shield-lock"></i> Your details go straight to our team — never shared.</p>
        </form>
      </div>
    </div>
  </div></section>
</main>
@endsection
