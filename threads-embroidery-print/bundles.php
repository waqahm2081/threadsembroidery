<?php
$page_title = 'Bundle Deals | Threads Embroidery & Print';
$page_description = 'Fixed-price branded apparel bundles for UK businesses, teams and start-ups. Pick your sizes, colours and branding — extra placements available as add-ons.';
$page_active = 'bundles';
$page_styles = ['pages.css', 'bundles.css'];
$page_scripts = ['bundles-data.js', 'bundles.js'];
$mount = false; // bundles.js mounts
$page_extra_body = <<<'HTML'
<div class="bmodal" id="bundleModal">
  <div class="bmodal-card">
    <div class="bmodal-head"><h3 class="h-3">Build Your Bundle</h3><button class="icon-btn" id="bundleClose"><i class="bi bi-x-lg"></i></button></div>
    <div class="bmodal-body" id="bundleBody"></div>
  </div>
</div>
HTML;
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>
<main>
  <section class="page-hero"><div class="container">
    <div class="breadcrumb"><a href="index.php">Home</a><i class="bi bi-chevron-right"></i><span>Bundle Deals</span></div>
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
    <div class="approve-note" data-reveal>
      <i class="bi bi-clipboard-check"></i>
      <div><b>Demo bundles for your approval.</b><p class="muted" style="margin:.2rem 0 0;font-size:.92rem">These six bundles (Solo, Top Up, Start Up, Safety, Starting Up Your Merch, Team) use placeholder contents and prices — confirm them and we'll lock them in.</p></div>
    </div>
    <div class="bundles-grid" id="bundlesGrid"></div>
  </div></section>

  <section class="section section--tight section--muted"><div class="container">
    <div class="section-head center" data-reveal>
      <span class="h-eyebrow">Need Something Custom?</span>
      <h2 class="display-2 display-2--xs mt-2">Bigger Order or a Bespoke Bundle?</h2>
      <p class="lead">Tell us what you need and we'll build a tailored package for your team, club or business.</p>
    </div>
    <div class="text-center"><a class="btn btn--lg" href="branding-enquiry.php"><i class="bi bi-envelope-paper"></i> Send an Enquiry</a></div>
  </div></section>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
