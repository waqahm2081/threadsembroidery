<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use App\Services\ShopCartService;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StripePaymentController extends Controller
{
    public function __construct(
        private StripePaymentService $stripe,
        private CartService $bundleCart,
        private ShopCartService $shopCart,
    ) {
    }

    public function config(): JsonResponse
    {
        $key = $this->stripe->publishableKey();
        if ($key === '') {
            throw ValidationException::withMessages([
                'payment' => 'Stripe publishable key is missing.',
            ]);
        }

        return response()->json([
            'publishableKey' => $key,
            'currency' => $this->stripe->currency(),
        ]);
    }

    public function createIntent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'context' => ['required', 'in:bundle,shop,uniform'],
            'email' => ['nullable', 'email', 'max:190'],
            'items' => ['nullable', 'array'],
            'items.*.variant_id' => ['required_with:items', 'integer', 'exists:school_product_variants,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:999'],
        ]);

        $context = $validated['context'];
        $email = $validated['email'] ?? auth()->user()?->email;

        [$amount, $metadata] = match ($context) {
            'bundle' => $this->bundleAmount(),
            'shop' => $this->shopAmount(),
            'uniform' => $this->uniformAmount($validated),
        };

        $intent = $this->stripe->createPaymentIntent($amount, $metadata, $email);

        return response()->json([
            'ok' => true,
            'clientSecret' => $intent['client_secret'],
            'paymentIntentId' => $intent['id'],
            'amount' => $amount,
            'currency' => $intent['currency'],
        ]);
    }

    /** @return array{0:float,1:array<string,string>} */
    private function bundleAmount(): array
    {
        $cart = $this->bundleCart->getCart();
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $totals = $this->bundleCart->totals($cart);

        return [
            (float) $totals['total'],
            [
                'context' => 'bundle',
                'cart_id' => (string) $cart->id,
            ],
        ];
    }

    /** @return array{0:float,1:array<string,string>} */
    private function shopAmount(): array
    {
        $cart = $this->shopCart->getCart();
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your shop cart is empty.']);
        }

        $total = (float) $cart->total();

        return [
            $total,
            [
                'context' => 'shop',
                'cart_id' => (string) $cart->id,
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array{0:float,1:array<string,string>}
     */
    private function uniformAmount(array $validated): array
    {
        $items = $validated['items'] ?? [];
        if ($items === []) {
            throw ValidationException::withMessages(['items' => 'Uniform cart is empty.']);
        }

        $subtotal = 0.0;
        foreach ($items as $row) {
            $variant = \App\Models\SchoolProductVariant::query()->findOrFail((int) $row['variant_id']);
            $subtotal += ((float) $variant->price) * (int) $row['quantity'];
        }
        $subtotal = round($subtotal, 2);
        $vat = round($subtotal * 0.20, 2);
        $delivery = $subtotal >= 50 ? 0.0 : 4.99;
        $total = round($subtotal + $vat + $delivery, 2);

        return [
            $total,
            [
                'context' => 'uniform',
                'user_id' => (string) (auth()->id() ?? ''),
            ],
        ];
    }
}
