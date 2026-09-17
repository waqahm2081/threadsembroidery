<?php

namespace App\Http\Controllers;

use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Services\ShopCartService;
use App\Services\ShopPricingService;
use App\Services\StripePaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopCheckoutController extends Controller
{
    public function __construct(
        private ShopCartService $cartService,
        private StripePaymentService $stripe,
    ) {
    }

    public function show(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        return view('shop.checkout', [
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $cart->subtotal(),
            'vat' => $cart->vatAmount(),
            'total' => $cart->total(),
            'vatPercent' => $cart->vatPercent(),
            'user' => auth()->user(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_zip' => ['required', 'string', 'max:20'],
            'billing_same_as_shipping' => ['nullable', 'boolean'],
            'billing_address_line1' => ['required_unless:billing_same_as_shipping,1', 'nullable', 'string', 'max:255'],
            'billing_city' => ['required_unless:billing_same_as_shipping,1', 'nullable', 'string', 'max:100'],
            'billing_state' => ['required_unless:billing_same_as_shipping,1', 'nullable', 'string', 'max:100'],
            'billing_zip' => ['required_unless:billing_same_as_shipping,1', 'nullable', 'string', 'max:20'],
            'billing_company_name' => ['nullable', 'string', 'max:255'],
            'additional_instructions' => ['nullable', 'string', 'max:2000'],
            'payment_intent_id' => ['required', 'string', 'max:190'],
        ]);

        $billingSame = $request->boolean('billing_same_as_shipping', true);
        $subtotalExVat = $cart->subtotal();
        $vatAmount = $cart->vatAmount();
        $total = $cart->total();

        $this->stripe->assertPaid($validated['payment_intent_id'], (float) $total);

        $order = DB::transaction(function () use ($cart, $validated, $billingSame, $subtotalExVat, $vatAmount, $total) {
            $order = ShopOrder::create([
                'order_number' => 'PS-'.strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => 'card',
                'stripe_payment_intent_id' => $validated['payment_intent_id'],
                'paid_at' => now(),
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'company_name' => $validated['company_name'] ?? null,
                'shipping_address_line1' => $validated['shipping_address_line1'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_zip' => $validated['shipping_zip'],
                'billing_same_as_shipping' => $billingSame,
                'billing_address_line1' => $billingSame ? $validated['shipping_address_line1'] : ($validated['billing_address_line1'] ?? null),
                'billing_city' => $billingSame ? $validated['shipping_city'] : ($validated['billing_city'] ?? null),
                'billing_state' => $billingSame ? $validated['shipping_state'] : ($validated['billing_state'] ?? null),
                'billing_zip' => $billingSame ? $validated['shipping_zip'] : ($validated['billing_zip'] ?? null),
                'billing_company_name' => $billingSame ? ($validated['company_name'] ?? null) : ($validated['billing_company_name'] ?? null),
                'additional_instructions' => $validated['additional_instructions'] ?? null,
                'subtotal' => $subtotalExVat,
                'vat_amount' => $vatAmount,
                'total' => $total,
            ]);

            foreach ($cart->items as $item) {
                ShopOrderItem::create([
                    'shop_order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'style_code' => $item->style_code,
                    'product_title' => $item->product_title,
                    'category_slug' => $item->category_slug,
                    'colour_name' => $item->colour_name,
                    'colour_code' => $item->colour_code,
                    'size' => $item->size,
                    'size_breakdown' => $item->size_breakdown,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'decoration_fee' => $item->decoration_fee,
                    'logo_setup_fee' => $item->logo_setup_fee,
                    'line_total' => $item->lineTotal(),
                    'design_data' => $item->design_data,
                    'design_file_path' => $item->design_file_path,
                    'design_assets' => $item->design_assets,
                ]);
            }

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('shop.checkout.confirmation', $order->order_number)
            ->with('success', 'Your order has been placed successfully!');
    }

    public function confirmation(string $orderNumber): View
    {
        $order = ShopOrder::where('order_number', $orderNumber)->with('items')->firstOrFail();

        return view('shop.checkout-confirmation', [
            'order' => $order,
        ]);
    }
}
