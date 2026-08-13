<?php
$page_title = 'Category | Threads Embroidery & Print';
$page_description = 'Browse customisable garments — add your logo via embroidery or DTF print. Free artwork setup, 10–14 day turnaround.';
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
    <div class="breadcrumb"><a href="index.php">Home</a><i class="bi bi-chevron-right"></i><a href="catalogue.php">Catalogue</a><i class="bi bi-chevron-right"></i><span data-cat-name>Category</span></div>
    <h1><span data-cat-name>Category</span></h1>
    <p class="lead">Every garment below is fully customisable with your logo — embroidery or DTF print, multiple placements, free artwork setup.</p>
  </div></section>

  <section class="section section--tight"><div class="container">
    <div class="shop-layout">
      <aside class="filters" id="filterPanel">
        <div class="flex justify-between items-center" style="margin-bottom:1rem">
          <b>Filters</b><button class="icon-btn filter-toggle" id="filterClose" onclick="document.getElementById('filterPanel').classList.remove('open')"><i class="bi bi-x-lg"></i></button>
        </div>
        <h4>Categories</h4>
        <div class="filter-list" id="catFilterList"></div>
        <h4>Colour</h4>
        <div class="swatch-filter" id="colourFilter"></div>
        <a class="btn btn--ghost btn--block mt-3" href="branding-enquiry.php" style="margin-top:1.4rem"><i class="bi bi-envelope-paper"></i> Bulk Enquiry</a>
      </aside>
      <div>
        <div class="shop-toolbar">
          <button class="btn btn--ghost filter-toggle" id="filterToggle"><i class="bi bi-sliders"></i> Filters</button>
          <span class="count" id="catCount"></span>
          <div class="flex gap-2 items-center">
            <label class="vat-toggle pill" style="cursor:pointer;gap:.5rem"><input type="checkbox" id="vatToggle" style="accent-color:var(--brand)"> inc. VAT</label>
            <select id="sortSelect" aria-label="Sort products">
              <option value="featured">Featured</option>
              <option value="new">New In</option>
              <option value="low">Price: Low to High</option>
              <option value="high">Price: High to Low</option>
            </select>
          </div>
        </div>
        <div class="prod-grid" id="categoryGrid"></div>
      </div>
    </div>
  </div></section>
</main>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/scripts.php';
