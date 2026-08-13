@extends('layouts.app')

@section('content')
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right"></i><span>Bundle Deals</span></div>
    <span class="h-eyebrow">Fixed-Price Bundle Deals</span>
    <h1 class="mt-2">Ready-Made Kits, One Simple Price</h1>
    <p class="lead">Skip the guesswork. Pick a bundle, choose your sizes, colours and branding, and you're set — with extra placements available as easy add-ons.</p>
    <div class="bundle-hero-strip">
      <span class="pill"><i class="bi bi-check-circle-fill text-brand"></i> Left chest branding included</span>
      <span class="pill"><i class="bi bi-check-circle-fill text-brand"></i> Free artwork setup</span>
      <span class="pill"><i class="bi bi-check-circle-fill text-brand"></i> Mix sizes freely</span>
      <span class="pill"><i class="bi bi-check-circle-fill text-brand"></i> 10–14 day turnaround</span>
    </div>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div class="bundles-grid" id="bundlesGrid"></div>
  </div></section>

  <section class="section section--tight section--muted"><div class="container">
    <div class="section-head center" data-reveal>
      <span class="h-eyebrow">Need Something Custom?</span>
      <h2 class="display-2 display-2--xs mt-2">Bigger Order or a Bespoke Bundle?</h2>
      <p class="lead">Tell us what you need and we'll build a tailored package for your team, club or business.</p>
    </div>
    <div class="text-center"><a class="btn btn--lg" href="{{ route('branding-enquiry') }}"><i class="bi bi-envelope-paper"></i> Send an Enquiry</a></div>
  </div></section>
</main>
@endsection
