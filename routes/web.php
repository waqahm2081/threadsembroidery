<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/branding-enquiry', [PageController::class, 'brandingEnquiry'])->name('branding-enquiry');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [PageController::class, 'login'])->name('login');
    Route::get('/signup', [PageController::class, 'signup'])->name('signup');
});

Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('forgot-password');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/user-dashboard', [UserDashboardController::class, 'index'])->name('user-dashboard');
    Route::post('/user-dashboard/profile', [ProfileController::class, 'update'])->name('user-dashboard.profile');
});

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::post('/cart/items/{itemId}/customize', [CartController::class, 'customize'])->name('cart.customize');
Route::delete('/cart/items/{itemId}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

Route::get('/shop/{categorySlug}/{productSlug}/customizer', [ProductController::class, 'customizer'])->name('shop.product.customizer');
Route::get('/shop/{categorySlug}/{productSlug}', [ProductController::class, 'show'])->name('shop.product');
Route::get('/shop/{categorySlug}', [ShopController::class, 'category'])->name('shop.category');

Route::redirect('/shop', '/shop/workwear')->name('shop');
Route::redirect('/product-detail', '/shop/workwear');
Route::redirect('/product-customizer', '/shop/workwear');
