<?php

namespace App\Http\Controllers;

use App\Services\BundleCatalogService;
use App\Services\CartService;

class PageController extends Controller
{
    public function __construct(
        private BundleCatalogService $bundleCatalog,
        private CartService $cartService,
    ) {
    }

    public function home()
    {
        return view('home', [
            'pageTitle' => 'Threads Embroidery & Print | Custom Embroidery & Printing UK',
            'pageDescription' => 'UK custom embroidery and print for businesses, schools and sports teams. Design online with our live customiser, browse the catalogue, or grab a fixed-price bundle. Free artwork setup, 10–14 day turnaround.',
            'pageActive' => 'home',
            'pageStyles' => ['home.css'],
            'pageScripts' => ['bundles-data.js', 'home.js'],
            'bundlesData' => $this->bundleCatalog->frontendPayload(),
            'mount' => false,
            'pageExtraHead' => <<<'HTML'
<meta property="og:title" content="Threads Embroidery & Print — Your Brand, Stitched Into Every Thread">
<meta property="og:description" content="Custom embroidery & printing for UK businesses, schools and sports teams. Free artwork setup, 10–14 day turnaround.">
<meta property="og:type" content="website">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"LocalBusiness","name":"Threads Embroidery & Print Ltd","image":"/assets/img/logo.png","telephone":"+44 7916 916369","email":"customerservice@threadsembroideryprint.co.uk","address":{"@type":"PostalAddress","addressLocality":"Cullompton","addressCountry":"GB"},"description":"Custom embroidery and print for businesses, schools and sports teams across the UK.","aggregateRating":{"@type":"AggregateRating","ratingValue":"4.9","reviewCount":"500"}}
</script>
HTML,
        ]);
    }

    public function about()
    {
        return view('about', [
            'pageTitle' => 'About Us | Threads Embroidery & Print',
            'pageDescription' => 'Threads Embroidery & Print is a UK-based, family-run custom embroidery and printing business built on trust, precision and professionalism — serving businesses, schools and sports teams.',
            'pageActive' => 'about',
            'pageStyles' => ['pages.css'],
            'mount' => ['active' => 'about'],
        ]);
    }

    public function services()
    {
        return view('services', [
            'pageTitle' => 'Services | Threads Embroidery & Print',
            'pageDescription' => "Custom embroidery, DTF printing, and a 'bring your own garment' branding service for businesses, schools and sports teams across the UK.",
            'pageActive' => 'services',
            'pageStyles' => ['pages.css'],
            'mount' => ['active' => 'services'],
        ]);
    }

    public function bundles()
    {
        return view('bundles', [
            'pageTitle' => 'Bundle Deals | Threads Embroidery & Print',
            'pageDescription' => 'Fixed-price branded apparel bundles for UK businesses, teams and start-ups. Pick your sizes, colours and branding — extra placements available as add-ons.',
            'pageActive' => 'bundles',
            'pageStyles' => ['pages.css', 'bundles.css'],
            'pageScripts' => ['bundles-data.js', 'bundles.js'],
            'bundlesData' => $this->bundleCatalog->frontendPayload(),
            'mount' => false,
            'pageExtraBody' => <<<'HTML'
<div class="bmodal" id="bundleModal">
  <div class="bmodal-card">
    <div class="bmodal-head"><h3 class="h-3">Build Your Bundle</h3><button class="icon-btn" id="bundleClose"><i class="bi bi-x-lg"></i></button></div>
    <div class="bmodal-body" id="bundleBody"></div>
  </div>
</div>
HTML,
        ]);
    }

    public function brandingEnquiry()
    {
        return view('branding-enquiry', [
            'pageTitle' => 'Branding Enquiry | Threads Embroidery & Print',
            'pageDescription' => "Enquire about custom embroidery or print — including our 'bring your own garment' service. Bulk, team and complex orders welcome.",
            'pageActive' => 'enquiry',
            'pageStyles' => ['pages.css'],
            'pageScripts' => ['forms.js'],
            'mount' => ['active' => 'enquiry', 'onReady' => 'THREADS.wireForms'],
        ]);
    }

    public function artworkStudio()
    {
        return view('artwork-studio', [
            'pageTitle' => 'Artwork Studio | Threads Embroidery & Print',
            'pageDescription' => 'Free artwork setup from Threads Embroidery & Print. Upload your logo and we will clean it up, match it to thread colours and get it ready to stitch or print.',
            'pageActive' => 'artwork-studio',
            'pageStyles' => ['studio.css'],
            'pageScripts' => [
                'artwork-studio/config.js',
                'artwork-studio/thread-chart.js',
                'artwork-studio/engine.js',
                'artwork-studio/studio.js',
            ],
            'mount' => false,
        ]);
    }

    public function contact()
    {
        return view('contact', [
            'pageTitle' => 'Contact Us | Threads Embroidery & Print',
            'pageDescription' => 'Get in touch with Threads Embroidery & Print — Cullompton, UK. Call, email, or send us a message.',
            'pageActive' => 'contact',
            'pageStyles' => ['pages.css'],
            'pageScripts' => ['forms.js'],
            'mount' => ['active' => 'contact', 'onReady' => 'THREADS.wireForms'],
        ]);
    }

    public function login()
    {
        return view('login', [
            'pageTitle' => 'Login / Sign Up | Threads Client Portal',
            'pageDescription' => 'Log in to your Threads client portal — saved artwork, order history and one-click reorders.',
            'pageActive' => '',
            'pageStyles' => ['pages.css', 'portal.css'],
            'pageScripts' => ['portal.js'],
            'mount' => ['active' => '', 'onReady' => 'THREADS.initLogin'],
            'authRoutes' => [
                'login' => route('login.submit'),
                'register' => route('register'),
                'portal' => route('portal'),
            ],
        ]);
    }

    public function cart()
    {
        return view('cart', [
            'pageTitle' => 'Your Cart | Threads Embroidery & Print',
            'pageDescription' => 'Review your custom branded garment order.',
            'pageActive' => '',
            'pageStyles' => ['pages.css', 'cart.css'],
            'pageScripts' => ['artwork-modal.js', 'cart.js'],
            'mount' => ['active' => ''],
            'cartBootstrap' => $this->cartService->toFrontend(),
        ]);
    }

    public function catalogue()
    {
        return view('catalogue', [
            'pageTitle' => 'Catalogue | Threads Embroidery & Print',
            'pageDescription' => 'Browse the Threads catalogue — workwear, schoolwear, sportswear, corporatewear, hospitality and more. Every garment fully customisable with your brand.',
            'pageActive' => 'catalogue',
            'pageStyles' => ['pages.css'],
            'pageScripts' => ['shop.js'],
            'mount' => false,
            'pageInlineJs' => "THREADS.shopInit('catalogue');",
        ]);
    }

    public function category()
    {
        return view('category', [
            'pageTitle' => 'Category | Threads Embroidery & Print',
            'pageDescription' => 'Browse customisable garments — add your logo via embroidery or DTF print. Free artwork setup, 10–14 day turnaround.',
            'pageActive' => 'catalogue',
            'pageStyles' => ['pages.css'],
            'pageScripts' => ['shop.js'],
            'mount' => false,
            'pageInlineJs' => "THREADS.shopInit('catalogue');",
        ]);
    }

    public function product()
    {
        return view('product', [
            'pageTitle' => 'Product | Threads Embroidery & Print',
            'pageDescription' => 'Customisable branded garment — embroidery or DTF print, free artwork setup, 10–14 day turnaround.',
            'pageActive' => 'catalogue',
            'pageStyles' => ['pages.css'],
            'pageScripts' => ['shop.js'],
            'mount' => false,
            'pageInlineJs' => "THREADS.shopInit('catalogue');",
        ]);
    }

    public function configurator()
    {
        return view('configurator', [
            'pageTitle' => 'Live Customiser | Threads Embroidery & Print',
            'pageDescription' => 'Design your branded garment live — pick a product and colour, upload your logo, choose placements and sizes, and get a production-ready order spec.',
            'pageActive' => 'catalogue',
            'pageStyles' => ['pages.css', 'configurator.css'],
            'pageScripts' => ['garment-templates.js', 'configurator.js'],
            'mount' => false,
            'pageExtraBody' => <<<'HTML'
<div class="modal-scrim" id="specModal">
  <div class="modal-card">
    <div class="modal-head"><h3 class="h-3">Order Specification</h3><button class="icon-btn" id="specClose"><i class="bi bi-x-lg"></i></button></div>
    <div style="padding:1.4rem" id="specBody"></div>
  </div>
</div>
HTML,
        ]);
    }

}
