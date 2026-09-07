<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private CartService $cartService,
        private StripePaymentService $stripe,
    ) {
    }

    public function store(Request $request)
    {
        $sameAsShipping = filter_var($request->input('billing_same_as_shipping', true), FILTER_VALIDATE_BOOLEAN);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'shipping_address_line1' => ['required', 'string', 'max:190'],
            'shipping_address_line2' => ['nullable', 'string', 'max:190'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_postcode' => ['required', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'max:120'],
            'billing_same_as_shipping' => ['nullable'],
            'billing_name' => [Rule::requiredIf(! $sameAsShipping), 'nullable', 'string', 'max:120'],
            'billing_company_name' => ['nullable', 'string', 'max:190'],
            'billing_address_line1' => [Rule::requiredIf(! $sameAsShipping), 'nullable', 'string', 'max:190'],
            'billing_address_line2' => ['nullable', 'string', 'max:190'],
            'billing_city' => [Rule::requiredIf(! $sameAsShipping), 'nullable', 'string', 'max:120'],
            'billing_postcode' => [Rule::requiredIf(! $sameAsShipping), 'nullable', 'string', 'max:20'],
            'billing_country' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'payment_intent_id' => ['required', 'string', 'max:190'],
        ]);

        $data['billing_same_as_shipping'] = $sameAsShipping;

        $cart = $this->cartService->getCart();
        $totals = $this->cartService->totals($cart);
        $this->stripe->assertPaid($data['payment_intent_id'], (float) $totals['total']);

        $data['payment_status'] = 'paid';
        $data['payment_method'] = 'card';
        $data['stripe_payment_intent_id'] = $data['payment_intent_id'];
        $data['paid_at'] = now();

        $order = $this->orderService->place($data);

        return response()->json([
            'ok' => true,
            'order_number' => $order->order_number,
            'redirect' => route('checkout.confirmation', $order->order_number),
        ]);
    }
}
