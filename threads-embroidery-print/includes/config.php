<?php
/**
 * Site-wide configuration and helpers.
 */
if (defined('THREADS_CONFIG_LOADED')) {
    return;
}
define('THREADS_CONFIG_LOADED', true);

$SITE = [
    'name'        => 'Threads Embroidery & Print',
    'legal_name'  => 'Threads Embroidery & Print Ltd',
    'phone'       => '+44 7916 916369',
    'phone_tel'   => '+447916916369',
    'email'       => 'customerservice@threadsembroideryprint.co.uk',
    'address'     => 'Cullompton, United Kingdom',
    'hours'       => 'Mon–Fri, 9:00am – 5:00pm',
    'hours_short' => 'Mon–Fri, 9am–5pm',
    'base'        => '', // relative base path (empty when site is at web root)
];

$NAV = [
    ['label' => 'Home',              'href' => 'index.php',              'key' => 'home'],
    ['label' => 'About Us',          'href' => 'about.php',              'key' => 'about'],
    ['label' => 'Services',          'href' => 'services.php',           'key' => 'services'],
    ['label' => 'Bundles',           'href' => 'bundles.php',            'key' => 'bundles'],
    ['label' => 'Branding Enquiry',  'href' => 'branding-enquiry.php',   'key' => 'enquiry'],
    ['label' => 'Contact',           'href' => 'contact.php',            'key' => 'contact'],
];

$TOPBAR_ITEMS = [
    ['bi-telephone-fill',     '+44 7916 916369'],
    ['bi-envelope-fill',      'customerservice@threadsembroideryprint.co.uk'],
    ['bi-clock-fill',         '10–14 Working Day Turnaround'],
    ['bi-brush-fill',         'Free Logo Artwork Setup on Every Order'],
    ['bi-truck',              'Free UK Delivery on Orders Over £150'],
    ['bi-patch-check-fill',   '100% Quality Guarantee'],
    ['bi-geo-alt-fill',       'Proudly UK Based & Family Run'],
];

$SOCIAL_LINKS = [
    [
        'label'  => 'Facebook',
        'icon'   => 'bi-facebook',
        'href'   => 'https://www.facebook.com/profile.php?id=61576908124134',
        'external' => true,
    ],
    [
        'label'  => 'Instagram',
        'icon'   => 'bi-instagram',
        'href'   => 'https://www.instagram.com/threadsembroidery/',
        'external' => true,
    ],
    [
        'label'  => 'TikTok',
        'icon'   => 'bi-tiktok',
        'href'   => 'https://www.tiktok.com/@threads.embroider0',
        'external' => true,
    ],
    [
        'label'  => 'Email',
        'icon'   => 'bi-envelope',
        'href'   => 'mailto:' . $SITE['email'],
        'external' => false,
    ],
];

// Load categories for mega menu / footer
$CATEGORIES = [];
$categoriesFile = dirname(__DIR__) . '/data/categories.json';
if (is_readable($categoriesFile)) {
    $json = json_decode(file_get_contents($categoriesFile), true);
    $CATEGORIES = $json['categories'] ?? [];
}

/** Escape for HTML output */
function e(?string $str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Build a site URL (relative) */
function url(string $path = ''): string {
    global $SITE;
    return $SITE['base'] . ltrim($path, '/');
}

/** Check if a nav key is the active page */
function is_active(string $key): bool {
    global $page_active;
    return isset($page_active) && $page_active === $key;
}
