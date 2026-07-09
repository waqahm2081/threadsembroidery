<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\OrderTrackingService;
use App\Services\PricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderTrackingService $trackingService,
    ) {
    }

    public function show(): View|RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $user = auth()->user();

        return view('checkout', [
            'cart' => $cart,
            'items' => $cart->items,
            'subtotal' => $cart->subtotal(),
            'vat' => $cart->vatAmount(),
            'total' => $cart->total(),
            'user' => $user,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
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
        ]);

        $billingSame = $request->boolean('billing_same_as_shipping', true);
        $subtotalExVat = $cart->subtotal();
        $vatAmount = PricingService::vatAmount($subtotalExVat);
        $total = PricingService::totalIncVat($subtotalExVat);

        $order = DB::transaction(function () use ($cart, $validated, $billingSame, $subtotalExVat, $vatAmount, $total) {
            $order = Order::create([
                'order_number' => 'TE-'.strtoupper(Str::random(8)),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'estimated_delivery_at' => now()->addDays(7),
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
                OrderItem::create([
                    'order_id' => $order->id,
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

            $this->trackingService->seedInitialEvent($order);

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('checkout.confirmation', $order->order_number)
            ->with('success', 'Your order has been placed successfully!');
    }

    public function confirmation(string $orderNumber): View
    {
        $order = Order::where('order_number', $orderNumber)->with('items')->firstOrFail();

        return view('checkout-confirmation', [
            'order' => $order,
        ]);
    }
}
