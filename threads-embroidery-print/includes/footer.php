<?php
/**
 * Site footer.
 */
require_once __DIR__ . '/config.php';
$year = (int) date('Y');
$footerCats = array_slice($CATEGORIES, 0, 6);
?>
<footer class="site-footer"><div class="container">
  <div class="footer-grid">
    <div class="footer-brand">
      <img src="<?= e(url('assets/img/logo.png')) ?>" alt="<?= e($SITE['name']) ?>">
      <p>UK's trusted custom embroidery and print specialists. We help businesses, schools and sports clubs look their best — with branded garments crafted to last.</p>
      <div class="footer-social">
<?php foreach ($SOCIAL_LINKS as $s):
  $ext = !empty($s['external']) ? ' target="_blank" rel="noopener noreferrer"' : '';
?>
        <a href="<?= e($s['href']) ?>"<?= $ext ?> aria-label="<?= e($s['label']) ?>"><i class="bi <?= e($s['icon']) ?>"></i></a>
<?php endforeach; ?>
      </div>
    </div>
    <div class="footer-col"><h4>Quick Links</h4>
      <a href="<?= e(url('index.php')) ?>">Home</a>
      <a href="<?= e(url('about.php')) ?>">About Us</a>
      <a href="<?= e(url('services.php')) ?>">Services</a>
      <a href="<?= e(url('bundles.php')) ?>">Bundle Deals</a>
      <a href="<?= e(url('branding-enquiry.php')) ?>">Branding Enquiry</a>
      <a href="<?= e(url('contact.php')) ?>">Contact Us</a>
    </div>
    <div class="footer-col"><h4>Categories</h4>
<?php foreach ($footerCats as $c): ?>
      <a href="<?= e(url('category.php?cat=' . rawurlencode($c['slug']))) ?>"><?= e($c['name']) ?></a>
<?php endforeach; ?>
    </div>
    <div class="footer-col"><h4>Get In Touch</h4>
      <ul class="footer-contact">
        <li><i class="bi bi-telephone-fill"></i><a href="tel:<?= e($SITE['phone_tel']) ?>"><?= e($SITE['phone']) ?></a></li>
        <li><i class="bi bi-envelope-fill"></i><a href="mailto:<?= e($SITE['email']) ?>"><?= e($SITE['email']) ?></a></li>
        <li><i class="bi bi-geo-alt-fill"></i><?= e($SITE['address']) ?></li>
        <li><i class="bi bi-clock-fill"></i><?= e($SITE['hours_short']) ?></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© <?= $year ?> <?= e($SITE['legal_name']) ?>. All rights reserved.</span>
    <span class="flex gap-2"><a href="#">Terms &amp; Conditions</a><a href="#">Privacy Policy</a></span>
  </div>
</div></footer>
