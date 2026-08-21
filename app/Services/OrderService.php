<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        private CartService $cartService,
        private NotificationMailService $mailNotifications,
    ) {
    }

    public function place(array $data): Order
    {
        $cart = $this->cartService->getCart()->load(['items.files']);
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $totals = $this->cartService->totals($cart);
        $sameAsShipping = filter_var($data['billing_same_as_shipping'] ?? true, FILTER_VALIDATE_BOOLEAN);

        $order = DB::transaction(function () use ($data, $cart, $totals, $sameAsShipping) {
            $order = Order::create([
                'order_number' => $this->uniqueOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'shipping_address_line1' => $data['shipping_address_line1'],
                'shipping_address_line2' => $data['shipping_address_line2'] ?? null,
                'shipping_city' => $data['shipping_city'],
                'shipping_postcode' => $data['shipping_postcode'],
                'shipping_country' => $data['shipping_country'] ?? 'United Kingdom',
                'billing_same_as_shipping' => $sameAsShipping,
                'billing_name' => $sameAsShipping ? $data['name'] : ($data['billing_name'] ?? null),
                'billing_company_name' => $sameAsShipping ? ($data['company_name'] ?? null) : ($data['billing_company_name'] ?? null),
                'billing_address_line1' => $sameAsShipping ? $data['shipping_address_line1'] : ($data['billing_address_line1'] ?? null),
                'billing_address_line2' => $sameAsShipping ? ($data['shipping_address_line2'] ?? null) : ($data['billing_address_line2'] ?? null),
                'billing_city' => $sameAsShipping ? $data['shipping_city'] : ($data['billing_city'] ?? null),
                'billing_postcode' => $sameAsShipping ? $data['shipping_postcode'] : ($data['billing_postcode'] ?? null),
                'billing_country' => $sameAsShipping
                    ? ($data['shipping_country'] ?? 'United Kingdom')
                    : ($data['billing_country'] ?? 'United Kingdom'),
                'notes' => $data['notes'] ?? null,
                'subtotal' => $totals['subtotal'],
                'vat_amount' => $totals['vat'],
                'delivery_fee' => $totals['delivery'],
                'total' => $totals['total'],
            ]);

            foreach ($cart->items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item->item_type,
                    'bundle_id' => $item->bundle_id,
                    'title' => $item->title,
                    'image_path' => $item->image_path,
                    'colour_name' => $item->colour_name,
                    'colour_hex' => $item->colour_hex,
                    'method' => $item->method,
                    'size_breakdown' => $item->size_breakdown,
                    'garments_snapshot' => $item->garments_snapshot,
                    'addons' => $item->addons,
                    'quantity' => $item->quantity,
                    'base_price' => $item->base_price,
                    'addon_total' => $item->addon_total,
                    'line_total' => $item->line_total,
                    'summary' => $item->summary,
                ]);

                foreach ($item->files as $file) {
                    $path = $this->persistArtworkForOrder($order->id, $orderItem->id, $file->path);

                    OrderItemFile::create([
                        'order_item_id' => $orderItem->id,
                        'placement_slug' => $file->placement_slug,
                        'placement_label' => $file->placement_label,
                        'path' => $path,
                        'original_name' => $file->original_name,
                    ]);
                }
            }

            // Safe to wipe cart artwork now — order copies live under order-artwork/
            $this->cartService->clear();

            return $order->load('items.files');
        });

        $this->mailNotifications->sendBundleOrder($order);

        return $order;
    }

    private function persistArtworkForOrder(int $orderId, int $orderItemId, string $sourcePath): string
    {
        $disk = Storage::disk('public');
        $filename = basename($sourcePath);
        $destination = "order-artwork/{$orderId}/{$orderItemId}/{$filename}";

        if ($sourcePath !== '' && $disk->exists($sourcePath)) {
            $disk->copy($sourcePath, $destination);

            return $destination;
        }

        // Fallback: keep original reference if the source is already missing
        return $sourcePath !== '' ? $sourcePath : $destination;
    }

    private function uniqueOrderNumber(): string
    {
        do {
            $number = 'TE-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
