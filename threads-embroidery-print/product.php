<?php
$page_title = 'Product | Threads Embroidery & Print';
$page_description = 'Customisable branded garment — embroidery or DTF print, free artwork setup, 10–14 day turnaround.';
$page_active = 'catalogue';
$page_styles = ['pages.css'];
$page_scripts = ['shop.js'];
$mount = false;
$page_inline_js = "THREADS.shopInit('catalogue');";
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="section" style="padding-top:clamp(28px,4vw,44px)"><div class="container">
    <div id="productRoot"></div>
  </div></section>
  <section class="section section--tight section--muted"><div class="container">
    <div class="section-head" data-reveal><span class="h-eyebrow">You May Also Like</span><h2 class="display-2 display-2--xs mt-2">Related Products</h2></div>
    <div class="prod-grid" id="relatedGrid"></div>
  </div></section>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
