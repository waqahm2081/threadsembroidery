<?php
/**
 * Contact detail cards (phone, email, location, hours, turnaround).
 * Used on contact.php; branding-enquiry has its own specialised cards.
 */
require_once __DIR__ . '/config.php';
?>
<div class="contact-cards" data-reveal>
  <div class="contact-card"><span class="ic"><i class="bi bi-telephone-fill"></i></span><div><b>Call Us</b><span><a href="tel:<?= e($SITE['phone_tel']) ?>"><?= e($SITE['phone']) ?></a></span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-envelope-fill"></i></span><div><b>Email Us</b><span><a href="mailto:<?= e($SITE['email']) ?>"><?= e($SITE['email']) ?></a></span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-geo-alt-fill"></i></span><div><b>Find Us</b><span><?= e($SITE['address']) ?></span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-clock-fill"></i></span><div><b>Opening Hours</b><span><?= e($SITE['hours']) ?></span></div></div>
  <div class="contact-card"><span class="ic"><i class="bi bi-truck"></i></span><div><b>Turnaround</b><span>10–14 working days · Free UK delivery over £150</span></div></div>
</div>
