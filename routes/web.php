<?php

use App\Http\Controllers\Api\BundleDataController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CheckoutController as ApiCheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandingEnquiryController;
use App\Http\Controllers\CheckoutPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/bundles', [PageController::class, 'bundles'])->name('bundles');
Route::get('/branding-enquiry', [PageController::class, 'brandingEnquiry'])->name('branding-enquiry');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/catalogue', [PageController::class, 'catalogue'])->name('catalogue');
Route::get('/category', [PageController::class, 'category'])->name('category');
Route::get('/product', [PageController::class, 'product'])->name('product');
Route::get('/configurator', fn () => redirect()->route('bundles'))->name('configurator');
Route::get('/checkout', [CheckoutPageController::class, 'show'])->name('checkout');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutPageController::class, 'confirmation'])->name('checkout.confirmation');

Route::middleware('guest')->group(function () {
    Route::get('/login', [PageController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/portal', [PortalController::class, 'show'])->name('portal');
    Route::get('/api/portal', [PortalController::class, 'data'])->name('api.portal.data');
    Route::post('/api/portal/profile', [PortalController::class, 'updateProfile'])->name('api.portal.profile');
    Route::post('/api/portal/password', [PortalController::class, 'updatePassword'])->name('api.portal.password');
    Route::post('/api/portal/artwork', [PortalController::class, 'storeArtwork'])->name('api.portal.artwork.store');
    Route::delete('/api/portal/artwork/{artworkId}', [PortalController::class, 'destroyArtwork'])->name('api.portal.artwork.destroy');
    Route::get('/api/portal/orders/{orderId}', [PortalController::class, 'order'])->name('api.portal.orders.show');
    Route::post('/api/portal/orders/{orderId}/reorder', [PortalController::class, 'reorder'])->name('api.portal.orders.reorder');
});

Route::prefix('api')->group(function () {
    Route::get('/bundles-data', BundleDataController::class)->name('api.bundles-data');
    Route::get('/cart', [ApiCartController::class, 'show'])->name('api.cart.show');
    Route::post('/cart/bundles', [ApiCartController::class, 'storeBundle'])->name('api.cart.bundles');
    Route::delete('/cart/items/{itemId}', [ApiCartController::class, 'destroyItem'])->name('api.cart.items.destroy');
    Route::post('/checkout', [ApiCheckoutController::class, 'store'])->name('api.checkout.store');
    Route::post('/branding-enquiries', [BrandingEnquiryController::class, 'store'])->name('api.branding-enquiries.store');
});

// Design-folder JS uses *.php paths — keep those working too
Route::get('/index.php', [PageController::class, 'home']);
Route::get('/about.php', [PageController::class, 'about']);
Route::get('/services.php', [PageController::class, 'services']);
Route::get('/bundles.php', [PageController::class, 'bundles']);
Route::get('/branding-enquiry.php', [PageController::class, 'brandingEnquiry']);
Route::get('/contact.php', [PageController::class, 'contact']);
Route::get('/login.php', [PageController::class, 'login'])->middleware('guest');
Route::get('/cart.php', [PageController::class, 'cart']);
Route::get('/catalogue.php', [PageController::class, 'catalogue']);
Route::get('/category.php', [PageController::class, 'category']);
Route::get('/product.php', [PageController::class, 'product']);
Route::get('/configurator.php', fn () => redirect()->route('bundles'));
Route::get('/portal.php', [PortalController::class, 'show'])->middleware('auth');
