@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>About Us</span></div>
    <span class="h-eyebrow">Who We Are</span>
    <h1 class="mt-2">Built on Trust.<br>Defined by Quality.</h1>
    <p class="lead">Reliable, high-quality custom embroidery and printing for businesses, schools and sports teams — with fast turnaround and outstanding service.</p>
  </div></section>

  <section class="section"><div class="container"><div class="split-grid split-grid--equal">
    <div class="split-media" data-reveal>
      <img src="{{ asset('assets/img/aboutmain.jpg') }}" alt="Threads embroidery workshop">
    </div>
    <div class="split-copy prose" data-reveal data-delay="1">
      <p>At Threads Embroidery &amp; Print, we specialise in delivering reliable, high-quality custom embroidery and printing services for businesses, schools and sports teams. Our goal is simple: provide exceptional branded apparel with fast turnaround times and outstanding customer service.</p>
      <p>We are built on a foundation of <b style="color:var(--text)">trust, precision and professionalism</b>. Founded on a passion for craftsmanship, everything we do is centred around your needs — whether you require a single embroidered polo or a full-scale uniform programme for hundreds of staff. We approach every project, large or small, with the same meticulous attention to detail. No shortcuts. No compromises.</p>
      <p class="muted">Proudly UK based and family run, from Cullompton — trusted by over 1000  clients nationwide.</p>
    </div>
  </div></div></section>

  <section class="section section--light"><div class="container">
    <div class="section-head center" data-reveal><span class="h-eyebrow">Our Values</span><h2 class="display-2 mt-2">What We Stand For</h2></div>
    <div class="icon-grid">
      <div class="icon-card" data-reveal><div class="ic"><i class="bi bi-shield-check"></i></div><h3>Trust &amp; Reliability</h3><p>Consistent quality you can count on, order after order. When we say 10–14 days, we mean it.</p></div>
      <div class="icon-card" data-reveal data-delay="1"><div class="ic"><i class="bi bi-award"></i></div><h3>Precision Craftsmanship</h3><p>Every stitch and print executed with exacting care on professional in-house machinery.</p></div>
      <div class="icon-card" data-reveal data-delay="2"><div class="ic"><i class="bi bi-people"></i></div><h3>Professionalism</h3><p>Responsive, helpful and straightforward to work with — from first enquiry to final delivery.</p></div>
    </div>
  </div></section>

  <section class="section"><div class="container">
    <div class="section-head center" data-reveal><span class="h-eyebrow">Our Work</span><h2 class="display-2 mt-2">A Look Inside</h2></div>
    <div class="about-gallery" data-reveal>
      <img src="{{ asset('assets/img/gallery/1.jpg') }}" alt="Branded garment">
      <img src="{{ asset('assets/img/gallery/4.jpg') }}" alt="Branded garment">
      <img src="{{ asset('assets/img/gallery/6.jpg') }}" alt="Branded garment">
    </div>
  </div></section>

@php
  $ctaTitle = "Let's Brand Your Team";
  $ctaText = "Design online, choose a bundle, or send us an enquiry — we'd love to help.";
  $ctaPrimary = ['label' => 'Shop Bundles', 'href' => route('bundles'), 'icon' => 'bi-collection'];
  $ctaSecondary = ['label' => 'Contact Us', 'href' => route('contact')];
@endphp
@include('partials.cta-band')
</main>
@endsection
