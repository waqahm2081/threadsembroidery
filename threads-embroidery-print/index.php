<?php
$page_title = 'Threads Embroidery & Print | Custom Embroidery & Printing UK';
$page_description = 'UK custom embroidery and print for businesses, schools and sports teams. Design online with our live customiser, browse the catalogue, or grab a fixed-price bundle. Free artwork setup, 10–14 day turnaround.';
$page_active = 'home';
$page_styles = ['home.css'];
$page_scripts = ['bundles-data.js', 'home.js'];
$mount = false; // home.js calls THREADS.mount
$page_extra_head = <<<'HTML'
<meta property="og:title" content="Threads Embroidery & Print — Your Brand, Stitched Into Every Thread">
<meta property="og:description" content="Custom embroidery & printing for UK businesses, schools and sports teams. Free artwork setup, 10–14 day turnaround.">
<meta property="og:type" content="website">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"LocalBusiness","name":"Threads Embroidery & Print Ltd","image":"assets/img/logo.png","telephone":"+44 7916 916369","email":"customerservice@threadsembroideryprint.co.uk","address":{"@type":"PostalAddress","addressLocality":"Cullompton","addressCountry":"GB"},"description":"Custom embroidery and print for businesses, schools and sports teams across the UK.","aggregateRating":{"@type":"AggregateRating","ratingValue":"4.9","reviewCount":"500"}}
</script>
HTML;
require __DIR__ . '/includes/head.php';
require __DIR__ . '/includes/header.php';
?>
<main>
  <!-- ===================== HERO ===================== -->
  <section class="hero" aria-label="Threads Embroidery main banner">
    <div class="hero-bg">
      <video autoplay muted loop playsinline poster="assets/img/aboutmain.jpg">
        <source src="assets/video/herovideo.mp4" type="video/mp4">
      </video>
    </div>
    <div class="container">
      <div class="hero-chips" data-reveal>
        <a class="hero-chip" href="services.php"><span class="ic"><i class="bi bi-vector-pen"></i></span><span><b>Embroidery</b><span>Left Chest &amp; Back</span></span></a>
        <a class="hero-chip" href="services.php"><span class="ic"><i class="bi bi-printer"></i></span><span><b>DTF Print</b><span>Vibrant Full Colour</span></span></a>
        <a class="hero-chip" href="category.php?cat=workwear"><span class="ic"><i class="bi bi-briefcase"></i></span><span><b>Workwear</b><span>Corporate Uniforms</span></span></a>
        <a class="hero-chip" href="category.php?cat=schoolwear"><span class="ic"><i class="bi bi-mortarboard"></i></span><span><b>Schoolwear</b><span>Blazers &amp; PE Kits</span></span></a>
        <a class="hero-chip" href="category.php?cat=sports"><span class="ic"><i class="bi bi-trophy"></i></span><span><b>Sportswear</b><span>Teams &amp; Clubs</span></span></a>
      </div>
      <div class="hero-badges" data-reveal data-delay="1">
        <span><i class="bi bi-star-fill star"></i> <b>4.9</b></span><span class="sep"></span>
        <span>1,000+ Clients</span><span class="sep"></span>
        <span>UK Nationwide</span><span class="sep"></span>
        <span class="text-brand">Free Artwork Setup</span>
      </div>
      <h1 class="display-1" data-reveal data-delay="1">Your Brand, Stitched Into<br><span class="text-brand">Every Thread.</span></h1>
      <p class="lead" data-reveal data-delay="2">Reliable, high-quality custom embroidery and printing for businesses, schools and sports teams — designed online, branded in-house, delivered fast.</p>
      <div class="hero-cta" data-reveal data-delay="2">
        <a class="btn btn--lg" href="configurator.php"><i class="bi bi-magic"></i> Design It Live</a>
        <a class="btn btn--ghost btn--lg" href="catalogue.php"><i class="bi bi-grid"></i> Browse Catalogue</a>
      </div>
      <div class="hero-assure" data-reveal data-delay="3">
        <span><i class="bi bi-check-circle-fill"></i> Free quote</span>
        <span><i class="bi bi-check-circle-fill"></i> Free artwork setup</span>
        <span><i class="bi bi-check-circle-fill"></i> 10–14 day turnaround</span>
        <span><i class="bi bi-check-circle-fill"></i> Free UK delivery over £150</span>
      </div>
    </div>
  </section>

  <!-- ===================== BRING YOUR OWN GARMENT ===================== -->
  <section class="section"><div class="container">
    <div class="byo" data-reveal>
      <div class="byo-grid">
        <div class="byo-media"><img src="assets/img/about-2.jpeg" alt="Branding a customer's own garment"></div>
        <div class="byo-body">
          <span class="h-eyebrow">03 — Bring Your Own Garment</span>
          <h2 class="display-2 display-2--sm mt-2">Already Have Garments?<br>We'll Brand Them For You.</h2>
          <p class="muted mt-2">Send us garments you've already purchased and our in-house team will apply professional embroidery or DTF print — chest, back, sleeves and more — to your exact specifications.</p>
          <div class="byo-feats">
            <div class="byo-feat"><i class="bi bi-box-seam"></i><div><b>Your Garments, Our Craft</b><span>Ideal for workwear, event kits and items sourced elsewhere.</span></div></div>
            <div class="byo-feat"><i class="bi bi-vector-pen"></i><div><b>Embroidery &amp; DTF Print</b><span>Left chest, centre chest, large back, sleeves.</span></div></div>
            <div class="byo-feat"><i class="bi bi-clock-history"></i><div><b>10–14 Working Days</b><span>Clear turnaround from confirmation to completion.</span></div></div>
          </div>
          <a class="btn btn--lg" href="branding-enquiry.php"><i class="bi bi-envelope-paper"></i> Start Your Enquiry</a>
        </div>
      </div>
    </div>
  </div></section>

  <!-- ===================== STATS ===================== -->
  <section class="stats"><div class="container-wide container"><div class="stats-grid">
    <div class="stat"><div class="n">5/5</div><div class="l">Average rating</div></div>
    <div class="stat"><div class="n">1,000+</div><div class="l">Orders completed</div></div>
    <div class="stat"><div class="n">10–14</div><div class="l">Day turnaround</div></div>
    <div class="stat"><div class="n">UK</div><div class="l">Nationwide delivery</div></div>
    <div class="stat"><div class="n">100%</div><div class="l">Quality guaranteed</div></div>
  </div></div></section>

  <!-- ===================== ABOUT ===================== -->
  <section class="section" id="about"><div class="container"><div class="about-grid">
    <div class="about-media" data-reveal>
      <img class="main" src="assets/img/about1.jpg" alt="Embroidery machine stitching a branded garment">
      <img class="float" src="assets/img/about2.jpg" alt="Finished branded workwear">
    </div>
    <div data-reveal data-delay="1">
      <span class="h-eyebrow">Who We Are</span>
      <h2 class="display-2 mt-2">Built on Trust.<br>Defined by Quality.</h2>
      <p class="lead mt-2">At Threads Embroidery &amp; Print, we're more than a uniform supplier — we're a dedicated branding partner for businesses, schools and sports teams across the UK. Our mission is simple: deliver exceptional custom embroidery and printing that represents your brand with pride, every single time.</p>
      <p class="muted mt-2">Founded on a passion for precision and craftsmanship, everything we do is built around your needs. Whether it's a single embroidered polo or a full uniform programme for hundreds of staff, we approach every project with the same meticulous attention to detail. No shortcuts. No compromises.</p>
      <div class="value-row">
        <div class="value"><span class="ic"><i class="bi bi-shield-check"></i></span><div><b>Trust &amp; Reliability</b><p>Consistent quality you can count on, order after order.</p></div></div>
        <div class="value"><span class="ic"><i class="bi bi-award"></i></span><div><b>Precision Craftsmanship</b><p>Every stitch and print executed with exacting care.</p></div></div>
      </div>
      <div class="hero-cta mt-3"><a class="btn" href="about.php">Our Story <i class="bi bi-arrow-right"></i></a><a class="btn btn--ghost" href="services.php">Our Services</a></div>
    </div>
  </div></div></section>

  <!-- ===================== LIVE CUSTOMIZER ===================== -->
  <section class="section cust"><div class="container"><div class="cust-grid">
    <div data-reveal>
      <span class="h-eyebrow">Live Product Customiser</span>
      <h2 class="display-2 mt-2">Design It. See It Live.</h2>
      <p class="lead mt-2">Pick your garment and colours, upload your logo, and watch it appear on the product in real time — then choose your placements and sizes and send us a production-ready order.</p>
      <div class="cust-steps">
        <div class="cust-step"><span class="num">1</span><div><b>Choose Garment &amp; Colour</b><p>Browse the full range of garments, fabrics and colourways.</p></div></div>
        <div class="cust-step"><span class="num">2</span><div><b>Upload Your Logo / Artwork</b><p>PNG, JPG, PDF or SVG — our team reviews every file, free setup included.</p></div></div>
        <div class="cust-step"><span class="num">3</span><div><b>Pick Placements &amp; Sizes</b><p>Left chest, back, sleeve and more — with our set professional sizing for a flawless finish.</p></div></div>
        <div class="cust-step"><span class="num">4</span><div><b>Real-Time Preview</b><p>See a photorealistic render before you order.</p></div></div>
      </div>
      <a class="btn btn--lg" href="configurator.php"><i class="bi bi-magic"></i> Start Customising</a>
      <div class="cust-note"><i class="bi bi-info-circle"></i> Logo sizing is set by our team to guarantee a consistent, professional result.</div>
    </div>
    <div data-reveal data-delay="1">
      <div class="cust-mock">
        <div class="bar"><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="url">threadsembroideryprint.co.uk / customiser</span></div>
        <div class="stage">
          <div class="preview">
            <div class="pv-top">
              <span class="pv-badge"><i class="bi bi-broadcast"></i> Live preview</span>
              <div class="pv-views">
                <button type="button" class="active">Front</button>
                <button type="button">Back</button>
              </div>
            </div>
            <div class="pv-stage">
              <img id="custMockImg" src="" alt="Garment preview">
              <div class="logo-chip"><i class="bi bi-image"></i> YOUR LOGO</div>
              <div class="pv-guides" aria-hidden="true">
                <span class="g-chest"></span>
                <span class="g-center"></span>
              </div>
            </div>
            <div class="pv-tools">
              <button type="button" aria-label="Zoom out"><i class="bi bi-zoom-out"></i></button>
              <button type="button" aria-label="Zoom in"><i class="bi bi-zoom-in"></i></button>
              <button type="button" aria-label="Reset view"><i class="bi bi-arrow-counterclockwise"></i></button>
            </div>
            <div class="pv-bottom">
              <div class="pv-swatches" aria-label="Colour options">
                <i class="on" style="background:#1a1a1a" title="Black"></i>
                <i style="background:#fff;border-color:#bbb" title="White"></i>
                <i style="background:#1e3a5f" title="Navy"></i>
                <i style="background:#c41e3a" title="Red"></i>
                <i style="background:#2e5a3c" title="Forest"></i>
                <span class="more">+12</span>
              </div>
              <div class="pv-upload"><i class="bi bi-cloud-arrow-up"></i> Drop logo</div>
            </div>
          </div>
          <div class="panel">
            <div class="panel-prod">
              <b id="custMockTitle">Softstyle Tee</b>
              <span class="muted">Gildan · Cotton</span>
            </div>
            <div class="row"><span class="muted">Method</span><span class="sel">Embroidery</span></div>
            <div class="row"><span class="muted">Position</span><span class="sel">Left Chest</span></div>
            <div class="row"><span class="muted">Size</span><span class="sel">80mm</span></div>
            <div class="row"><span class="muted">Colour</span><span class="sel sel--sw"><i style="background:#1a1a1a"></i> Black</span></div>
            <div class="row"><span class="muted">Qty</span><span class="sel">25</span></div>
            <div class="tot"><span>Est. total</span><b>£187.50</b></div>
            <div class="panel-note"><i class="bi bi-shield-check"></i> Free artwork setup</div>
          </div>
        </div>
      </div>
    </div>
  </div></div></section>

  <!-- ===================== HOW IT WORKS (light) ===================== -->
  <section class="section section--light"><div class="container">
    <div class="section-head center" data-reveal>
      <span class="h-eyebrow">How It Works</span>
      <h2 class="display-2 mt-2">Order in Minutes, Delivered Fast</h2>
      <p class="lead">From live customisation to your doorstep — a clear four-step journey trusted by 1,000+ UK businesses, schools and clubs.</p>
    </div>
    <div class="steps-grid">
      <div class="step-card" data-reveal><div class="sn">01</div><div class="ic"><i class="bi bi-palette"></i></div><b>Choose &amp; Customise</b><p>Browse the catalogue, pick your garment and colours, then preview your logo live.</p></div>
      <div class="step-card" data-reveal data-delay="1"><div class="sn">02</div><div class="ic"><i class="bi bi-cloud-arrow-up"></i></div><b>Upload Your Logo</b><p>Add your artwork with placement and size details. We review every file — free setup included.</p></div>
      <div class="step-card" data-reveal data-delay="2"><div class="sn">03</div><div class="ic"><i class="bi bi-vector-pen"></i></div><b>We Brand Your Order</b><p>Embroidered or printed in-house with precision machinery, then quality-checked before dispatch.</p></div>
      <div class="step-card" data-reveal data-delay="3"><div class="sn">04</div><div class="ic"><i class="bi bi-truck"></i></div><b>Delivered to You</b><p>Free UK delivery to your door. Typical turnaround is 10–14 working days from confirmation.</p></div>
    </div>
    <div class="text-center mt-4" data-reveal><a class="btn btn--light btn--lg" href="configurator.php">Start Your First Order <i class="bi bi-arrow-right"></i></a></div>
  </div></section>

  <!-- ===================== BUNDLES ===================== -->
  <section class="section section--muted"><div class="container">
    <div class="section-head center" data-reveal>
      <span class="h-eyebrow">Fixed-Price Bundle Deals</span>
      <h2 class="display-2 mt-2">Ready-Made Kits, One Simple Price</h2>
      <p class="lead">Skip the guesswork — pick a bundle, choose your sizes, colours and branding, and you're set. Add extra placements any time.</p>
    </div>
    <div class="bundle-strip" id="homeBundles"></div>
    <div class="text-center mt-4" data-reveal><a class="btn btn--lg" href="bundles.php">See All Bundle Deals <i class="bi bi-arrow-right"></i></a></div>
  </div></section>

  <!-- ===================== SOCIAL ===================== -->
  <section class="section section--muted socialsection"><div class="container">
    <div class="social-band" data-reveal>
      <div class="section-head center">
        <span class="h-eyebrow">Stay Connected</span>
        <h2 class="display-2 mt-2">Follow Threads</h2>
        <p class="lead">See recent work, behind-the-scenes stitching, and updates — find us on the channels below.</p>
      </div>
      <div class="social-links">
<?php foreach ($SOCIAL_LINKS as $s):
  $ext = !empty($s['external']) ? ' target="_blank" rel="noopener noreferrer"' : '';
?>
        <a class="social-link" href="<?= e($s['href']) ?>"<?= $ext ?>>
          <span class="ic"><i class="bi <?= e($s['icon']) ?>"></i></span>
          <span class="lb"><?= e($s['label']) ?></span>
        </a>
<?php endforeach; ?>
      </div>
    </div>
  </div></section>

  <!-- ===================== COMPARE ===================== -->
  <section class="section"><div class="container">
    <div class="section-head center" data-reveal>
      <span class="h-eyebrow">Why Choose Us</span>
      <h2 class="display-2 mt-2">See How We Stack Up</h2>
      <p class="lead">Not all branding providers are equal. Here's how Threads compares.</p>
    </div>
    <div class="compare-wrap" data-reveal><table class="compare">
      <thead><tr>
        <th style="text-align:left"></th>
        <th class="us">Threads<br>Embroidery &amp; Print</th>
        <th>Generic Online<br>Printers</th>
        <th>Local<br>Freelancers</th>
        <th>DIY / Home<br>Printing</th>
      </tr></thead>
      <tbody>
        <tr><td class="feat">In-House UK Production</td><td class="us"><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-x-lg no"></i></td><td><i class="bi bi-dash-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td></tr>
        <tr><td class="feat">Live Customiser</td><td class="us"><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-dash-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td></tr>
        <tr><td class="feat">Free Artwork Setup</td><td class="us"><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-x-lg no"></i></td><td><i class="bi bi-dash-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td></tr>
        <tr><td class="feat">Quality Guarantee</td><td class="us"><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-dash-lg no"></i></td><td><i class="bi bi-dash-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td></tr>
        <tr><td class="feat">Bulk &amp; Team Orders</td><td class="us"><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-x-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td></tr>
        <tr><td class="feat">Free UK Delivery over £150</td><td class="us"><i class="bi bi-check-lg yes"></i></td><td><i class="bi bi-dash-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td><td><i class="bi bi-x-lg no"></i></td></tr>
      </tbody>
    </table></div>
  </div></section>

  <!-- ===================== FEATURED PRODUCTS (temporarily hidden) ===================== -->
  <?php if (false): ?>
  <section class="section"><div class="container">
    <div class="section-head section-head--toolbar" data-reveal>
      <div><span class="h-eyebrow">Popular Right Now</span><h2 class="display-2 mt-2">Featured Products</h2></div>
      <label class="vat-toggle pill" style="cursor:pointer;gap:.6rem"><input type="checkbox" id="vatToggle" style="accent-color:var(--brand)"> Show prices inc. VAT</label>
    </div>
    <div class="prod-grid" id="homeProducts"></div>
    <div class="text-center mt-4" data-reveal><a class="btn btn--lg" href="catalogue.php">Shop All Products <i class="bi bi-arrow-right"></i></a></div>
  </div></section>
  <?php endif; ?>

  <!-- ===================== VIDEO ===================== -->
  <section class="section section--muted"><div class="container"><div class="video-grid">
    <div class="video-frame" data-reveal><video autoplay muted loop playsinline poster="assets/img/about1.jpg"><source src="assets/video/video.mp4" type="video/mp4"></video></div>
    <div class="video-copy" data-reveal data-delay="1">
      <span class="h-eyebrow">See Us In Action</span>
      <h2 class="display-2 mt-2">Craftsmanship You Can See &amp; Feel</h2>
      <p class="muted mt-2">Every stitch and print is crafted with care and precision. We work with businesses, schools and clubs across the UK — delivering branded garments built to last and designed to impress.</p>
      <ul class="video-check">
        <li><i class="bi bi-check-circle-fill"></i> State-of-the-art embroidery and print machinery</li>
        <li><i class="bi bi-check-circle-fill"></i> In-house artwork team — free setup on every order</li>
        <li><i class="bi bi-check-circle-fill"></i> 10–14 working day turnaround with free UK delivery</li>
        <li><i class="bi bi-check-circle-fill"></i> Trusted by 1,000+ clients nationwide</li>
      </ul>
      <a class="btn btn--lg" href="branding-enquiry.php">Get a Free Quote <i class="bi bi-arrow-right"></i></a>
    </div>
  </div></div></section>

  <!-- ===================== TESTIMONIALS ===================== -->
  <section class="section"><div class="container">
    <div class="section-head center" data-reveal>
      <span class="h-eyebrow">Client Reviews</span>
      <h2 class="display-2 mt-2">Trusted by Thousands</h2>
      <p class="lead">Don't take our word for it — here's what our clients say.</p>
    </div>
    <div class="testi-slider">
      <div class="testi-grid" id="homeTesti"></div>
      <div class="testi-nav" id="homeTestiNav" hidden>
        <button type="button" data-dir="-1" aria-label="Previous review"><i class="bi bi-chevron-left"></i></button>
        <button type="button" data-dir="1" aria-label="Next review"><i class="bi bi-chevron-right"></i></button>
      </div>
      <div class="testi-dots" id="homeTestiDots" hidden></div>
    </div>
  </div></section>

  <!-- ===================== GALLERY ===================== -->
  <section class="section section--tight gallery-section">
    <div class="container">
      <div class="section-head center" data-reveal><span class="h-eyebrow">Our Work</span><h2 class="display-2 mt-2">Recent Branded Orders</h2></div>
    </div>
    <div class="gallery-bleed" data-reveal>
      <div class="gallery-grid">
        <a href="assets/img/gallery/1.jpg" class="g-lg" data-lightbox><img src="assets/img/gallery/1.jpg" alt="Branded polo shirts"></a>
        <a href="assets/img/gallery/2.jpg" class="g-w" data-lightbox><img src="assets/img/gallery/2.jpg" alt="Embroidery in progress"></a>
        <a href="assets/img/gallery/3.jpg" data-lightbox><img src="assets/img/gallery/3.jpg" alt="Custom embroidered cap"></a>
        <a href="assets/img/gallery/4.jpg" data-lightbox><img src="assets/img/gallery/4.jpg" alt="Branded cafe apron"></a>
        <a href="assets/img/gallery/5.jpg" class="g-w" data-lightbox><img src="assets/img/gallery/5.jpg" alt="Finished embroidery detail"></a>
        <a href="assets/img/gallery/6.jpg" class="g-w" data-lightbox><img src="assets/img/gallery/6.jpg" alt="Tagged branded garments"></a>
      </div>
    </div>
  </section>

<?php
require __DIR__ . '/includes/cta-band.php';
?>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
