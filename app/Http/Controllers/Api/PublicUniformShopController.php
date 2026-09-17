<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolProduct;
use App\Models\SchoolUniformOrder;
use App\Models\SchoolUniformOrderItem;
use App\Services\NotificationMailService;
use App\Services\StripePaymentService;
use App\Services\UniformSchoolAccess;
use App\Support\UniformPricing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PublicUniformShopController extends Controller
{
    public function __construct(
        private UniformSchoolAccess $access,
        private NotificationMailService $mailNotifications,
        private StripePaymentService $stripe,
    ) {
    }

    public function unlock(Request $request, School $school): JsonResponse
    {
        abort_unless($school->is_active, 404);

        $validated = $request->validate([
            'pin' => ['required', 'string', 'max:10'],
        ]);

        if (! $this->access->matches($school, $validated['pin'])) {
            throw ValidationException::withMessages([
                'pin' => 'That PIN is incorrect. Please try again.',
            ]);
        }

        $this->access->unlock($school);

        return response()->json([
            'ok' => true,
            'school' => $school->toFrontend(),
        ]);
    }

    public function school(School $school): JsonResponse
    {
        abort_unless($school->is_active, 404);
        $this->access->assertUnlocked($school);

        $categories = $school->allCategories()
            ->where('is_active', true)
            ->with(['products' => function ($q) {
                $q->where('is_active', true)
                    ->with(['variants', 'images'])
                    ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $uncategorised = $school->allProducts()
            ->where('is_active', true)
            ->whereNull('school_product_category_id')
            ->with(['variants', 'images'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'school' => $school->toFrontend(),
            'unlocked' => true,
            'categories' => $categories->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
                'description' => $cat->description,
                'products' => $cat->products->map->toFrontend()->values()->all(),
            ])->values()->all(),
            'uncategorised' => $uncategorised->map->toFrontend()->values()->all(),
        ]);
    }

    public function product(SchoolProduct $product): JsonResponse
    {
        abort_unless($product->is_active, 404);
        $product->load(['variants', 'images', 'school', 'category']);
        abort_unless($product->school && $product->school->is_active, 404);
        $this->access->assertUnlocked($product->school);

        return response()->json([
            'product' => $product->toFrontend(),
            'school' => $product->school->toFrontend(),
        ]);
    }

    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => ['nullable', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:120'],
            'shipping_postcode' => ['required', 'string', 'max:20'],
            'shipping_country' => ['nullable', 'string', 'max:60'],
            'billing_same_as_shipping' => ['nullable', 'boolean'],
            'billing_name' => ['nullable', 'string', 'max:190'],
            'billing_company_name' => ['nullable', 'string', 'max:190'],
            'billing_address_line1' => ['nullable', 'string', 'max:255'],
            'billing_address_line2' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:120'],
            'billing_postcode' => ['nullable', 'string', 'max:20'],
            'billing_country' => ['nullable', 'string', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_intent_id' => ['required', 'string', 'max:190'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.variant_id' => ['required', 'exists:school_product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ]);

        $sameBilling = $request->boolean('billing_same_as_shipping', true);
        if (! $sameBilling) {
            $request->validate([
                'billing_name' => ['required', 'string', 'max:190'],
                'billing_address_line1' => ['required', 'string', 'max:255'],
                'billing_city' => ['required', 'string', 'max:120'],
                'billing_postcode' => ['required', 'string', 'max:20'],
            ]);
        }

        $subtotal = 0;
        $lineItems = [];
        $schoolId = $validated['school_id'] ?? null;

        foreach ($validated['items'] as $item) {
            $variant = \App\Models\SchoolProductVariant::with('product.school')->findOrFail($item['variant_id']);
            $product = $variant->product;
            if (! $product || ! $product->school) {
                throw ValidationException::withMessages([
                    'items' => 'One or more cart items are no longer available. Please refresh and try again.',
                ]);
            }

            $this->access->assertUnlocked($product->school);

            $lineTotal = $variant->price * $item['quantity'];
            $subtotal += $lineTotal;
            $lineItems[] = [
                'variant' => $variant,
                'product' => $product,
                'quantity' => $item['quantity'],
                'lineTotal' => $lineTotal,
            ];
            if (! $schoolId) {
                $schoolId = $product->school_id;
            }
        }

        $school = null;
        if ($schoolId) {
            $school = School::query()->find($schoolId);
            if ($school) {
                $this->access->assertUnlocked($school);
            }
        }

        $totals = UniformPricing::total((float) $subtotal, $school);
        $vat = $totals['vat'];
        $delivery = $totals['delivery'];
        $total = $totals['total'];
        $subtotal = $totals['subtotal'];

        $this->stripe->assertPaid($validated['payment_intent_id'], (float) $total);

        $order = DB::transaction(function () use ($validated, $sameBilling, $lineItems, $schoolId, $subtotal, $vat, $delivery, $total) {
            $order = SchoolUniformOrder::create([
                'order_number' => SchoolUniformOrder::generateNumber(),
                'user_id' => Auth::id(),
                'school_id' => $schoolId,
                'status' => 'pending',
                'payment_status' => 'paid',
                'payment_method' => 'card',
                'stripe_payment_intent_id' => $validated['payment_intent_id'],
                'paid_at' => now(),
                'customer_name' => $validated['name'],
                'customer_email' => $validated['email'],
                'customer_phone' => $validated['phone'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'shipping_address_line1' => $validated['shipping_address_line1'],
                'shipping_address_line2' => $validated['shipping_address_line2'] ?? null,
                'shipping_city' => $validated['shipping_city'],
                'shipping_postcode' => $validated['shipping_postcode'],
                'shipping_country' => $validated['shipping_country'] ?? 'United Kingdom',
                'billing_same_as_shipping' => $sameBilling,
                'billing_name' => $sameBilling ? null : ($validated['billing_name'] ?? null),
                'billing_company_name' => $sameBilling ? null : ($validated['billing_company_name'] ?? null),
                'billing_address_line1' => $sameBilling ? null : ($validated['billing_address_line1'] ?? null),
                'billing_address_line2' => $sameBilling ? null : ($validated['billing_address_line2'] ?? null),
                'billing_city' => $sameBilling ? null : ($validated['billing_city'] ?? null),
                'billing_postcode' => $sameBilling ? null : ($validated['billing_postcode'] ?? null),
                'billing_country' => $sameBilling ? null : ($validated['billing_country'] ?? null),
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'vat_amount' => $vat,
                'delivery_fee' => $delivery,
                'total' => $total,
            ]);

            foreach ($lineItems as $li) {
                SchoolUniformOrderItem::create([
                    'school_uniform_order_id' => $order->id,
                    'school_product_id' => $li['product']->id,
                    'school_product_variant_id' => $li['variant']->id,
                    'product_name' => $li['product']->name,
                    'size' => $li['variant']->size,
                    'quantity' => $li['quantity'],
                    'unit_price' => $li['variant']->price,
                    'line_total' => $li['lineTotal'],
                ]);
            }

            return $order;
        });

        $this->mailNotifications->sendUniformOrder($order->load(['items', 'school']));

        return response()->json([
            'ok' => true,
            'order_number' => $order->order_number,
            'total' => $order->total,
        ]);
    }
}
