<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\SchoolController as AdminSchoolController;
use App\Http\Controllers\Admin\SchoolProductCategoryController as AdminSchoolCategoryController;
use App\Http\Controllers\Admin\SchoolProductController as AdminSchoolProductController;
use App\Http\Controllers\Admin\SchoolUniformOrderController as AdminSchoolUniformOrderController;
use App\Http\Controllers\Admin\ShopOrderController as AdminShopOrderController;
use App\Http\Controllers\Api\UniformShopController;
use App\Http\Controllers\Admin\BrandingEnquiryController as AdminBrandingEnquiryController;
use App\Http\Controllers\Admin\BundleController as AdminBundleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PlacementOptionController as AdminPlacementOptionController;
use App\Http\Controllers\Admin\ProductCustomizerController as AdminProductCustomizerController;
use App\Http\Controllers\Admin\ProductQuoteController as AdminProductQuoteController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SizeGuideController as AdminSizeGuideController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\BundleDataController;
use App\Http\Controllers\Api\CartController as ApiCartController;
use App\Http\Controllers\Api\CheckoutController as ApiCheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandingEnquiryController;
use App\Http\Controllers\CheckoutPageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopCartController;
use App\Http\Controllers\ShopCheckoutController;
use App\Http\Controllers\ShopController;
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

Route::get('/shop', [ShopController::class, 'index'])->name('shop');

// Product catalogue shop (pc_products) — separate cart/orders from bundles & uniforms
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/cart', [ShopCartController::class, 'index'])->name('cart');
    Route::post('/cart', [ShopCartController::class, 'store'])->name('cart.store');
    Route::post('/cart/items/{itemId}/customize', [ShopCartController::class, 'customize'])->name('cart.customize');
    Route::delete('/cart/items/{itemId}', [ShopCartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [ShopCheckoutController::class, 'show'])->name('checkout');
    Route::post('/checkout', [ShopCheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/confirmation/{orderNumber}', [ShopCheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    Route::get('/{categorySlug}/products', [ShopController::class, 'categoryProducts'])->name('category.products');
    Route::get('/{categorySlug}/{productSlug}/customizer', [ProductController::class, 'customizer'])->name('product.customizer');
    Route::get('/{categorySlug}/{productSlug}', [ProductController::class, 'show'])->name('product');
    Route::get('/{categorySlug}', [ShopController::class, 'category'])->name('category');
});

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

    // Uniform shop API
    Route::get('/api/portal/uniform/schools', [UniformShopController::class, 'schools'])->name('api.uniform.schools');
    Route::get('/api/portal/uniform/schools/{school}', [UniformShopController::class, 'school'])->name('api.uniform.school');
    Route::get('/api/portal/uniform/products/{product}', [UniformShopController::class, 'product'])->name('api.uniform.product');
    Route::post('/api/portal/uniform/checkout', [UniformShopController::class, 'checkout'])->name('api.uniform.checkout');
    Route::get('/api/portal/uniform/orders', [UniformShopController::class, 'orders'])->name('api.uniform.orders');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::resource('bundles', AdminBundleController::class)->except(['show']);
        Route::resource('size-guides', AdminSizeGuideController::class)->except(['show']);
        Route::resource('placements', AdminPlacementOptionController::class)->except(['show']);
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        Route::get('/shop-orders', [AdminShopOrderController::class, 'index'])->name('shop-orders.index');
        Route::get('/shop-orders/{shopOrder}', [AdminShopOrderController::class, 'show'])->name('shop-orders.show');
        Route::patch('/shop-orders/{shopOrder}/status', [AdminShopOrderController::class, 'updateStatus'])->name('shop-orders.status');
        Route::get('/shop-orders/{shopOrder}/items/{item}/design-download', [AdminShopOrderController::class, 'downloadDesign'])->name('shop-orders.design.download');

        Route::get('/enquiries', [AdminBrandingEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [AdminBrandingEnquiryController::class, 'show'])->name('enquiries.show');
        Route::put('/enquiries/{enquiry}', [AdminBrandingEnquiryController::class, 'update'])->name('enquiries.update');

        Route::get('/product-customizer', AdminProductCustomizerController::class)->name('customizer');
        Route::get('/product-quotes', [AdminProductQuoteController::class, 'index'])->name('product-quotes.index');
        Route::post('/product-quotes', [AdminProductQuoteController::class, 'store'])->name('product-quotes.store');
        Route::get('/product-quotes/{productQuote}', [AdminProductQuoteController::class, 'show'])->name('product-quotes.show');
        Route::delete('/product-quotes/{productQuote}', [AdminProductQuoteController::class, 'destroy'])->name('product-quotes.destroy');

        // Uniform Shop admin
        Route::resource('schools', AdminSchoolController::class)->except(['show']);
        Route::resource('school-categories', AdminSchoolCategoryController::class)->except(['show'])->parameters(['school-categories' => 'schoolCategory']);
        Route::resource('school-products', AdminSchoolProductController::class)->except(['show'])->parameters(['school-products' => 'schoolProduct']);
        Route::delete('/school-products/images/{image}', [AdminSchoolProductController::class, 'destroyImage'])->name('school-products.images.destroy');
        Route::get('/school-uniform-orders', [AdminSchoolUniformOrderController::class, 'index'])->name('school-uniform-orders.index');
        Route::get('/school-uniform-orders/{schoolUniformOrder}', [AdminSchoolUniformOrderController::class, 'show'])->name('school-uniform-orders.show');
        Route::patch('/school-uniform-orders/{schoolUniformOrder}/status', [AdminSchoolUniformOrderController::class, 'updateStatus'])->name('school-uniform-orders.status');

        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    });
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
