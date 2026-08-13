<?php
/**
 * Shared <head> — expects $page_title, $page_description;
 * optional: $page_styles (array), $page_extra_head (string), $page_body_class
 */
require_once __DIR__ . '/config.php';
$page_title       = $page_title       ?? ($SITE['name']);
$page_description = $page_description ?? '';
$page_styles      = $page_styles      ?? ['pages.css'];
$page_extra_head  = $page_extra_head  ?? '';
$page_body_class  = $page_body_class  ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?></title>
<?php if ($page_description): ?>
<meta name="description" content="<?= e($page_description) ?>">
<?php endif; ?>
<link rel="icon" href="<?= e(url('assets/img/logo.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= e(url('css/styles.css')) ?>">
<?php foreach ((array)$page_styles as $css): ?>
<link rel="stylesheet" href="<?= e(url('css/' . ltrim($css, '/'))) ?>">
<?php endforeach; ?>
<?= $page_extra_head ?>
</head>
<body<?= $page_body_class ? ' class="' . e($page_body_class) . '"' : '' ?>>
