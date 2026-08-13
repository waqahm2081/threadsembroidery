<?php
/**
 * Reusable CTA band section.
 *
 * Optional vars:
 *   $cta_title, $cta_text
 *   $cta_primary   — ['label'=>…, 'href'=>…, 'icon'=>…]
 *   $cta_secondary — ['label'=>…, 'href'=>…] or null
 */
$cta_title     = $cta_title     ?? 'Ready to Brand Your Team?';
$cta_text      = $cta_text      ?? 'Design online in minutes, grab a fixed-price bundle, or send us an enquiry — free artwork setup on every order.';
$cta_primary   = $cta_primary   ?? ['label' => 'Start Designing', 'href' => 'configurator.php', 'icon' => 'bi-magic'];
$cta_secondary = $cta_secondary ?? ['label' => 'View Bundles', 'href' => 'bundles.php'];
?>
<section class="section section--tight"><div class="container"><div class="cta-band" data-reveal>
  <h2 class="display-2"><?= e($cta_title) ?></h2>
  <p><?= e($cta_text) ?></p>
  <div class="hero-cta">
    <a class="btn btn--light btn--lg" href="<?= e(url($cta_primary['href'])) ?>"><?php if (!empty($cta_primary['icon'])): ?><i class="bi <?= e($cta_primary['icon']) ?>"></i> <?php endif; ?><?= e($cta_primary['label']) ?></a>
<?php if ($cta_secondary): ?>
    <a class="btn btn--dark btn--lg" href="<?= e(url($cta_secondary['href'])) ?>"><?= e($cta_secondary['label']) ?></a>
<?php endif; ?>
  </div>
</div></div></section>
<?php
unset($cta_title, $cta_text, $cta_primary, $cta_secondary);
?>
