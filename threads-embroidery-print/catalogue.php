<?php
require_once __DIR__ . '/includes/config.php';
$catCount = count($CATEGORIES);
$page_title = 'Catalogue | Threads Embroidery & Print';
$page_description = 'Browse the Threads catalogue — workwear, schoolwear, sportswear, corporatewear, hospitality and more. Every garment fully customisable with your brand.';
$page_active = 'catalogue';
$page_styles = ['pages.css'];
$page_scripts = ['shop.js'];
$mount = false;
$page_inline_js = "THREADS.shopInit('catalogue');";
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a><i class="bi bi-chevron-right"></i><span>Catalogue</span></div>
    <span class="h-eyebrow">Browse by Category</span>
    <h1 class="mt-2">Our Full Catalogue</h1>
    <p class="lead">From workwear to schoolwear — <?= (int)$catCount ?> categories, every garment fully customisable with your brand via embroidery or DTF print.</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div class="cat-grid" id="catalogueGrid"></div>
    <div class="note-box" data-reveal>
      <i class="bi bi-search-heart"></i>
      <div><b>Can't find what you're looking for?</b><p class="muted" style="font-size:.92rem;margin:0">We stock thousands of garments beyond what's shown here. <a class="link-arrow" style="display:inline" href="contact.php">Contact us</a> and we'll source it for you.</p></div>
    </div>
  </div></section>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
