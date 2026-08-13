@php
  $ctaTitle = $ctaTitle ?? 'Ready to Brand Your Team?';
  $ctaText = $ctaText ?? 'Design online in minutes, grab a fixed-price bundle, or send us an enquiry — free artwork setup on every order.';
  $ctaPrimary = $ctaPrimary ?? ['label' => 'Shop Bundles', 'href' => route('bundles'), 'icon' => 'bi-collection'];
  $ctaSecondary = $ctaSecondary ?? ['label' => 'View Bundles', 'href' => route('bundles')];
@endphp
<section class="section section--tight"><div class="container"><div class="cta-band" data-reveal>
  <h2 class="display-2">{{ $ctaTitle }}</h2>
  <p>{{ $ctaText }}</p>
  <div class="hero-cta">
    <a class="btn btn--light btn--lg" href="{{ $ctaPrimary['href'] }}">@if(!empty($ctaPrimary['icon']))<i class="bi {{ $ctaPrimary['icon'] }}"></i> @endif{{ $ctaPrimary['label'] }}</a>
@if ($ctaSecondary)
    <a class="btn btn--dark btn--lg" href="{{ $ctaSecondary['href'] }}">{{ $ctaSecondary['label'] }}</a>
@endif
  </div>
</div></div></section>
