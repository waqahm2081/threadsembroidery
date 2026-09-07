<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CartService;

class CheckoutPageController extends Controller
{
    public function show(CartService $cartService)
    {
        $cart = $cartService->toFrontend();
        if (empty($cart['items'])) {
            return redirect()->route('cart');
        }

        return view('checkout', [
            'pageTitle' => 'Checkout | Threads Embroidery & Print',
            'pageDescription' => 'Complete your branded garment order.',
            'pageActive' => '',
            'pageStyles' => ['pages.css', 'cart.css', 'checkout.css'],
            'pageScripts' => ['artwork-modal.js', 'stripe-checkout.js', 'checkout.js'],
            'mount' => ['active' => ''],
            'cartBootstrap' => $cart,
        ]);
    }

    public function confirmation(string $orderNumber)
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->with('items.files')
            ->firstOrFail();

        return view('checkout-confirmation', [
            'pageTitle' => 'Order Confirmed | Threads Embroidery & Print',
            'pageDescription' => 'Your order has been received.',
            'pageActive' => '',
            'pageStyles' => ['pages.css', 'cart.css', 'checkout.css'],
            'pageScripts' => ['artwork-modal.js'],
            'mount' => ['active' => ''],
            'order' => $order,
        ]);
    }
}
