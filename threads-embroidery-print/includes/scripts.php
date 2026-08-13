<?php
/**
 * Shared closing scripts.
 *
 * Optional vars:
 *   $page_scripts   — extra JS files under /js (array of filenames)
 *   $mount          — false to skip auto-mount; or array ['active'=>'…', 'onReady'=>'THREADS.fn']
 *   $page_inline_js — raw JS string run after scripts (no <script> tags)
 *   $page_extra_body — HTML before </body> (modals, page styles)
 */
require_once __DIR__ . '/config.php';
$page_scripts   = $page_scripts   ?? [];
$mount          = $mount          ?? false;
$page_inline_js = $page_inline_js ?? '';
$page_extra_body = $page_extra_body ?? '';
?>
<?= $page_extra_body ?>
<script src="<?= e(url('js/data.js')) ?>"></script>
<script src="<?= e(url('js/main.js')) ?>"></script>
<?php foreach ((array)$page_scripts as $js): ?>
<script src="<?= e(url('js/' . ltrim($js, '/'))) ?>"></script>
<?php endforeach; ?>
<?php if ($mount !== false):
  $active  = e($mount['active'] ?? ($page_active ?? ''));
  $onReady = $mount['onReady'] ?? null;
  $opts = "active:'{$active}',base:''";
  if ($onReady) {
      $opts .= ',onReady:' . $onReady;
  }
?>
<script>THREADS.mount({<?= $opts ?>});</script>
<?php endif; ?>
<?php if ($page_inline_js): ?>
<script><?= $page_inline_js ?></script>
<?php endif; ?>
</body>
</html>
