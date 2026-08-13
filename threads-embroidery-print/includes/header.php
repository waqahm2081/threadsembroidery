<?php
/**
 * Site header: topbar, main nav, mobile drawer, search overlay.
 * Expects $page_active (nav key) and $CATEGORIES / $NAV from config.
 */
require_once __DIR__ . '/config.php';
$page_active = $page_active ?? '';
$B = $SITE['base'];
?>
<div class="topbar"><div class="marquee"><?php
  $topRow = '';
  foreach ($TOPBAR_ITEMS as [$icon, $text]) {
      $topRow .= '<span class="m-item"><i class="bi ' . e($icon) . '"></i>' . e($text) . '</span><span class="m-sep">✦</span>';
  }
  echo $topRow . $topRow;
?></div></div>

<header class="site-header" id="siteHeader"><div class="container"><nav class="nav">
  <a class="nav-logo" href="<?= e(url('index.php')) ?>"><img src="<?= e(url('assets/img/logo.png')) ?>" width="391" height="96" alt="<?= e($SITE['name']) ?>"></a>
  <ul class="nav-links">
<?php foreach ($NAV as $n):
  $act = is_active($n['key']) ? ' active' : '';
  if (!empty($n['mega'])): ?>
    <li class="has-mega"><a class="<?= trim($act) ?>" href="<?= e(url($n['href'])) ?>"><?= e($n['label']) ?> <i class="bi bi-chevron-down" style="font-size:.7em"></i></a>
      <div class="mega">
        <div class="mega-head"><div><span class="h-eyebrow">Browse by Category</span><h3 class="h-3" style="margin-top:.4rem">Our Full Catalogue</h3></div>
        <a class="btn btn--ghost" href="<?= e(url('catalogue.php')) ?>">View Full Catalogue <i class="bi bi-arrow-right"></i></a></div>
        <div class="mega-grid">
<?php foreach ($CATEGORIES as $c): ?>
          <a href="<?= e(url('category.php?cat=' . rawurlencode($c['slug']))) ?>"><span class="n"><?= e($c['name']) ?></span><span class="c"><?= (int)$c['total'] ?></span></a>
<?php endforeach; ?>
        </div>
        <p class="muted" style="margin-top:16px;font-size:.86rem">Can't find what you're looking for? <a class="link-arrow" href="<?= e(url('contact.php')) ?>" style="display:inline">Contact us</a> and we'll source it for you.</p>
      </div></li>
<?php else: ?>
    <li><a class="<?= trim($act) ?>" href="<?= e(url($n['href'])) ?>"><?= e($n['label']) ?></a></li>
<?php endif; endforeach; ?>
  </ul>
  <div class="nav-actions">
    <button class="icon-btn search-btn" aria-label="Search products" data-search-open><i class="bi bi-search"></i></button>
    <a class="icon-btn" href="<?= e(url('cart.php')) ?>" aria-label="View cart"><i class="bi bi-bag"></i><span class="badge" data-cart-badge style="display:none">0</span></a>
    <span id="navAuthSlot"><a class="btn nav-cta" href="<?= e(url('login.php')) ?>"><i class="bi bi-person"></i> Login / Signup</a></span>
    <button class="icon-btn hamburger" aria-label="Open menu" data-drawer-open><i class="bi bi-list" style="font-size:1.5rem"></i></button>
  </div>
</nav></div></header>

<div class="drawer" data-drawer>
  <div class="scrim" data-drawer-close></div>
  <div class="panel">
    <div class="d-top"><img src="<?= e(url('assets/img/logo.png')) ?>" style="height:36px;width:auto;max-width:none;object-fit:contain" alt="Threads"><button class="icon-btn" data-drawer-close aria-label="Close menu"><i class="bi bi-x-lg"></i></button></div>
    <nav>
<?php foreach ($NAV as $n): ?>
      <a href="<?= e(url($n['href'])) ?>"><?= e($n['label']) ?></a>
<?php endforeach; ?>
    </nav>
    <div class="d-cats"><details><summary>All Categories <i class="bi bi-chevron-down"></i></summary>
      <div class="d-cats-list">
<?php foreach ($CATEGORIES as $c): ?>
        <a href="<?= e(url('category.php?cat=' . rawurlencode($c['slug']))) ?>"><span class="n"><?= e($c['name']) ?></span><span class="muted">(<?= (int)$c['total'] ?>)</span></a>
<?php endforeach; ?>
      </div>
    </details></div>
    <a class="btn btn--block mt-3" id="drawerAuthBtn" href="<?= e(url('login.php')) ?>"><i class="bi bi-person"></i> Login / Signup</a>
  </div>
</div>

<div class="search-overlay" data-search><div class="search-box">
  <div class="field"><i class="bi bi-search" style="color:var(--brand);font-size:1.3rem"></i>
    <input type="text" placeholder="Search products, categories…" data-search-input aria-label="Search">
    <button class="icon-btn" data-search-close aria-label="Close search"><i class="bi bi-x-lg"></i></button></div>
  <div class="search-results" data-search-results hidden></div>
</div></div>
