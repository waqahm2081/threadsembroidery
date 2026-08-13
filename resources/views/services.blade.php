@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Services</span></div>
    <span class="h-eyebrow">What We Do</span>
    <h1 class="mt-2">Branding, Done Properly</h1>
    <p class="lead">From a single embroidered polo to a full uniform programme — professional embroidery and print, in-house, with free artwork setup on every order.</p>
  </div></section>

  <section class="section"><div class="container"><div class="split-grid">
    <div class="split-media" data-reveal>
      <img src="{{ asset('assets/img/about2.jpg') }}" alt="Embroidery service">
    </div>
    <div class="split-copy" data-reveal data-delay="1">
      <span class="h-eyebrow">01 — Embroidery</span>
      <h2 class="display-2 display-2--sm mt-2">Premium Stitched Branding</h2>
      <p class="muted mt-2">A durable, professional finish that holds up wash after wash. Ideal for polos, workwear, caps and corporate uniforms — left chest, back, sleeves and more.</p>
      <ul class="pd-feature-list mt-3">
        <li><i class="bi bi-check-circle-fill"></i> Clean stitch paths optimised for every fabric</li>
        <li><i class="bi bi-check-circle-fill"></i> Sharp small text and fine detail</li>
        <li><i class="bi bi-check-circle-fill"></i> Free logo digitising &amp; artwork setup</li>
      </ul>
      <a class="btn mt-3" href="{{ route('bundles') }}">Order with Embroidery <i class="bi bi-arrow-right"></i></a>
    </div>
  </div></div></section>

  <section class="section section--muted"><div class="container"><div class="ba-grid">
    <div class="ba-compare" id="baCompare" data-reveal>
      <img class="before-img" src="{{ asset('assets/img/ba-before.png') }}" alt="Plain garment before branding">
      <div class="after-wrap" id="baAfter"><img src="{{ asset('assets/img/ba-after.png') }}" alt="Garment after professional embroidery"></div>
      <div class="ba-handle" id="baHandle"><span class="grip"><i class="bi bi-arrows-collapse" style="transform:rotate(90deg)"></i></span></div>
      <span class="ba-lbl b">Before</span><span class="ba-lbl a">After</span>
    </div>
    <div data-reveal data-delay="1">
      <span class="h-eyebrow">Before &amp; After</span>
      <h2 class="display-2 mt-2">Plain Garment.<br>Professional Brand.</h2>
      <p class="lead mt-2">Drag the slider to see the Threads difference — why our embroidery and print wins on every run.</p>
      <div class="ba-points">
        <div class="ba-point"><span class="ic"><i class="bi bi-bezier2"></i></span><div><b>Clean Stitch Paths</b><p>Optimised for your fabric — no puckering, uneven edges or gaps around your logo.</p></div></div>
        <div class="ba-point"><span class="ic"><i class="bi bi-fonts"></i></span><div><b>Sharp Small Text</b><p>Legible lettering and fine detail on polos, caps and workwear.</p></div></div>
        <div class="ba-point"><span class="ic"><i class="bi bi-bullseye"></i></span><div><b>Perfect Registration</b><p>Precise placement and colour alignment — consistent from the first piece to the last.</p></div></div>
      </div>
    </div>
  </div></div></section>

  <section class="section"><div class="container"><div class="split-grid split-grid--rev">
    <div class="split-media" data-reveal>
      <img src="{{ asset('assets/img/gallery/6.jpg') }}" alt="Printing service">
    </div>
    <div class="split-copy" data-reveal data-delay="1">
      <span class="h-eyebrow">02 — DTF Print</span>
      <h2 class="display-2 display-2--sm mt-2">Vibrant Full-Colour Print</h2>
      <p class="muted mt-2">Bold, full-colour designs with crisp edges — perfect for merch, event kits, sports and anything with detailed or multi-colour artwork.</p>
      <ul class="pd-feature-list mt-3">
        <li><i class="bi bi-check-circle-fill"></i> Full-colour DTF print, front and back</li>
        <li><i class="bi bi-check-circle-fill"></i> Great for gradients, photos and fine detail</li>
        <li><i class="bi bi-check-circle-fill"></i> Durable, flexible finish</li>
      </ul>
      <a class="btn mt-3" href="{{ route('bundles') }}">Order with Print <i class="bi bi-arrow-right"></i></a>
    </div>
  </div></div></section>

  <section class="section"><div class="container">
    <div class="byo" data-reveal>
      <div class="byo-grid">
        <div class="byo-media"><img src="{{ asset('assets/img/about-2.jpeg') }}" alt="Branding a customer's own garment"></div>
        <div class="byo-body">
          <span class="h-eyebrow">03 — Bring Your Own Garment</span>
          <h2 class="display-2 display-2--sm mt-2">Already Have Garments?<br>We'll Brand Them For You.</h2>
          <p class="muted mt-2">Send us garments you've already purchased and our in-house team will apply professional embroidery or DTF print — chest, back, sleeves and more — to your exact specifications.</p>
          <div class="byo-feats">
            <div class="byo-feat"><i class="bi bi-box-seam"></i><div><b>Your Garments, Our Craft</b><span>Ideal for workwear, event kits and items sourced elsewhere.</span></div></div>
            <div class="byo-feat"><i class="bi bi-vector-pen"></i><div><b>Embroidery &amp; DTF Print</b><span>Left chest, centre chest, large back, sleeves.</span></div></div>
            <div class="byo-feat"><i class="bi bi-clock-history"></i><div><b>10–14 Working Days</b><span>Clear turnaround from confirmation to completion.</span></div></div>
          </div>
          <a class="btn btn--lg" href="{{ route('branding-enquiry') }}"><i class="bi bi-envelope-paper"></i> Start Your Enquiry</a>
        </div>
      </div>
    </div>
  </div></section>

  <section class="section section--light"><div class="container">
    <div class="section-head center" data-reveal><span class="h-eyebrow">Who We Serve</span><h2 class="display-2 mt-2">Branding for Every Team</h2></div>
    <div class="icon-grid">
      <div class="icon-card" data-reveal><div class="ic"><i class="bi bi-briefcase"></i></div><h3>Businesses</h3><p>Corporate uniforms, workwear and staff kit that looks unified and professional.</p></div>
      <div class="icon-card" data-reveal data-delay="1"><div class="ic"><i class="bi bi-mortarboard"></i></div><h3>Schools</h3><p>Blazers, PE kits and uniform programmes — with easy reordering for departments.</p></div>
      <div class="icon-card" data-reveal data-delay="2"><div class="ic"><i class="bi bi-trophy"></i></div><h3>Sports Teams</h3><p>Team kit, training wear and squad numbers — mix sizes and names in one order.</p></div>
    </div>
  </div></section>
</main>
@endsection
