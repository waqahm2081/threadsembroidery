@php $site = config('site'); @endphp
<div class="contact-cards" data-reveal>
  <div class="contact-card"><span class="ic"><i class="bi bi-telephone-fill"></i></span><div><b>Call Us</b><span><a href="tel:{{ $site['phone_tel'] }}">{{ $site['phone'] }}</a></span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-envelope-fill"></i></span><div><b>Email Us</b><span><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-geo-alt-fill"></i></span><div><b>Find Us</b><span>{{ $site['address'] }}</span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-clock-fill"></i></span><div><b>Opening Hours</b><span>{{ $site['hours'] }}</span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-truck"></i></span><div><b>Turnaround</b><span>10–14 working days · Free UK delivery over £150</span></div></div>
</div>
