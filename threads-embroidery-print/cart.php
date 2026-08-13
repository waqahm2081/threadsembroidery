<?php
$page_title = 'Your Cart | Threads Embroidery & Print';
$page_description = 'Review your custom branded garment order.';
$page_active = '';
$page_styles = ['pages.css'];
$page_scripts = ['shop.js'];
$mount = false;
$page_extra_head = '<style>@media(max-width:820px){.cart-layout{grid-template-columns:1fr!important}}</style>';
$page_inline_js = "THREADS.shopInit('catalogue');";
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a><i class="bi bi-chevron-right"></i><span>Cart</span></div>
    <h1>Your Cart</h1>
  </div></section>
  <section class="section section--tight"><div class="container"><div id="cartRoot"></div></div></section>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
