<?php

$categoriesPath = public_path('data/categories.json');
$categories = [];
if (is_readable($categoriesPath)) {
    $json = json_decode(file_get_contents($categoriesPath), true);
    $categories = $json['categories'] ?? [];
}

return [
    'name' => 'Threads Embroidery & Print',
    'legal_name' => 'Threads Embroidery & Print Ltd',
    'phone' => '+44 7916 916369',
    'phone_tel' => '+447916916369',
    'email' => 'customerservice@threadsembroideryprint.co.uk',
    'address' => 'Cullompton, United Kingdom',
    'hours' => 'Mon–Fri, 9:00am – 5:00pm',
    'hours_short' => 'Mon–Fri, 9am–5pm',

    'nav' => [
        ['label' => 'Home', 'route' => 'home', 'key' => 'home'],
        ['label' => 'About Us', 'route' => 'about', 'key' => 'about'],
        ['label' => 'Services', 'route' => 'services', 'key' => 'services'],
        ['label' => 'Bundles', 'route' => 'bundles', 'key' => 'bundles'],
        ['label' => 'Artwork Studio', 'route' => 'artwork-studio', 'key' => 'artwork-studio'],
        ['label' => 'Shop', 'route' => 'shop', 'key' => 'shop', 'mega' => true],
        ['label' => 'Branding Enquiry', 'route' => 'branding-enquiry', 'key' => 'enquiry'],
        ['label' => 'Contact', 'route' => 'contact', 'key' => 'contact'],
    ],

    'topbar' => [
        ['bi-telephone-fill', '+44 7916 916369'],
        ['bi-envelope-fill', 'customerservice@threadsembroideryprint.co.uk'],
        ['bi-clock-fill', '10–14 Working Day Turnaround'],
        ['bi-brush-fill', 'Free Logo Artwork Setup on Every Order'],
        ['bi-truck', 'Free UK Delivery on Orders Over £150'],
        ['bi-patch-check-fill', '100% Quality Guarantee'],
        ['bi-geo-alt-fill', 'Proudly UK Based & Family Run'],
    ],

    'social' => [
        [
            'label' => 'Facebook',
            'icon' => 'bi-facebook',
            'href' => 'https://www.facebook.com/profile.php?id=61576908124134',
            'external' => true,
        ],
        [
            'label' => 'Instagram',
            'icon' => 'bi-instagram',
            'href' => 'https://www.instagram.com/threadsembroidery/',
            'external' => true,
        ],
        [
            'label' => 'TikTok',
            'icon' => 'bi-tiktok',
            'href' => 'https://www.tiktok.com/@threads.embroider0',
            'external' => true,
        ],
        [
            'label' => 'Email',
            'icon' => 'bi-envelope',
            'href' => 'mailto:customerservice@threadsembroideryprint.co.uk',
            'external' => false,
        ],
    ],

    'categories' => $categories,
];
