<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/signup', [PageController::class, 'signup'])->name('signup');
Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('forgot-password');
Route::get('/user-dashboard', [PageController::class, 'userDashboard'])->name('user-dashboard');

Route::get('/shop/{categorySlug}/{productSlug}/customizer', [ProductController::class, 'customizer'])->name('shop.product.customizer');
Route::get('/shop/{categorySlug}/{productSlug}', [ProductController::class, 'show'])->name('shop.product');
Route::get('/shop/{categorySlug}', [ShopController::class, 'category'])->name('shop.category');

Route::redirect('/shop', '/shop/workwear')->name('shop');
Route::redirect('/product-detail', '/shop/workwear');
Route::redirect('/product-customizer', '/shop/workwear');
