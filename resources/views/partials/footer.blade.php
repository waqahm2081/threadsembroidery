@php
  $site = config('site');
  $categories = $site['categories'] ?? [];
  $social = $site['social'] ?? [];
  $footerCats = array_slice($categories, 0, 6);
  $year = (int) date('Y');
@endphp
<footer class="site-footer"><div class="container">
  <div class="footer-grid">
    <div class="footer-brand">
      <img src="{{ asset('assets/img/logo.png') }}" alt="{{ $site['name'] }}">
      <p>UK's trusted custom embroidery and print specialists. We help businesses, schools and sports clubs look their best — with branded garments crafted to last.</p>
      <div class="footer-social">
@foreach ($social as $s)
        <a href="{{ $s['href'] }}"@if(!empty($s['external'])) target="_blank" rel="noopener noreferrer"@endif aria-label="{{ $s['label'] }}"><i class="bi {{ $s['icon'] }}"></i></a>
@endforeach
      </div>
    </div>
    <div class="footer-col"><h4>Quick Links</h4>
      <a href="{{ route('home') }}">Home</a>
      <a href="{{ route('about') }}">About Us</a>
      <a href="{{ route('services') }}">Services</a>
      <a href="{{ route('bundles') }}">Bundle Deals</a>
      <a href="{{ route('branding-enquiry') }}">Branding Enquiry</a>
      <a href="{{ route('contact') }}">Contact Us</a>
    </div>
    <div class="footer-col"><h4>Categories</h4>
@foreach ($footerCats as $c)
      <a href="{{ url('/category?cat=' . rawurlencode($c['slug'])) }}">{{ $c['name'] }}</a>
@endforeach
    </div>
    <div class="footer-col"><h4>Get In Touch</h4>
      <ul class="footer-contact">
        <li><i class="bi bi-telephone-fill"></i><a href="tel:{{ $site['phone_tel'] }}">{{ $site['phone'] }}</a></li>
        <li><i class="bi bi-envelope-fill"></i><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li>
        <li><i class="bi bi-geo-alt-fill"></i>{{ $site['address'] }}</li>
        <li><i class="bi bi-clock-fill"></i>{{ $site['hours_short'] }}</li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© {{ $year }} {{ $site['legal_name'] }}. All rights reserved.</span>
    <span class="flex gap-2"><a href="#">Terms &amp; Conditions</a><a href="#">Privacy Policy</a></span>
  </div>
</div></footer>
