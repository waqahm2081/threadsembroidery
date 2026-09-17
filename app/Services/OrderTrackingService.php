<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\DesignPreview;
use Illuminate\Support\Str;

class OrderTrackingService
{
    public const TIMELINE_STEPS = [
        'Paid',
        'Being Prepared',
        'In Production',
        'Ready to Pack',
        'Dispatched',
        'Delivered',
    ];

    public function seedInitialEvent(Order $order): void
    {
        $order->trackingEvents()->create([
            'event' => 'Paid',
            'occurred_at' => $order->created_at,
            'sort_order' => 0,
        ]);
    }

    public function toTrackingPayload(Order $order): array
    {
        $events = $order->trackingEvents;

        if ($events->isNotEmpty()) {
            return $this->buildFromEvents($order, $events);
        }

        return $this->buildFromStatus($order);
    }

    protected function buildFromEvents(Order $order, $events): array
    {
        $activeIndex = $this->activeIndexForStatus($order->status);
        $steps = [];

        foreach (self::TIMELINE_STEPS as $index => $label) {
            $event = $events->firstWhere('event', $label);

            if ($event) {
                $state = $event->occurred_at ? 'done' : ($index === $activeIndex ? 'active' : 'pending');
                $steps[] = [
                    'event' => $label,
                    'date' => $event->occurred_at
                        ? $event->occurred_at->format('d M Y, H:i')
                        : 'Pending',
                    'state' => $state,
                ];
            } else {
                $state = $index < $activeIndex ? 'done' : ($index === $activeIndex ? 'active' : 'pending');
                $steps[] = [
                    'event' => $label,
                    'date' => $state === 'pending' ? 'Pending' : '—',
                    'state' => $state,
                ];
            }
        }

        return [
            'desc' => $this->orderDescription($order),
            'status' => \App\Support\OrderFulfillmentStatus::label($order->status),
            'eta' => $this->etaLabel($order),
            'steps' => $steps,
        ];
    }

    protected function buildFromStatus(Order $order): array
    {
        $activeIndex = $this->activeIndexForStatus($order->status);
        $steps = [];

        foreach (self::TIMELINE_STEPS as $index => $label) {
            $state = $index < $activeIndex ? 'done' : ($index === $activeIndex ? 'active' : 'pending');

            $date = 'Pending';
            if ($state === 'done') {
                $date = $index === 0
                    ? $order->created_at->format('d M Y, H:i')
                    : '—';
            }

            $steps[] = [
                'event' => $label,
                'date' => $date,
                'state' => $state,
            ];
        }

        return [
            'desc' => $this->orderDescription($order),
            'status' => \App\Support\OrderFulfillmentStatus::label($order->status),
            'eta' => $this->etaLabel($order),
            'steps' => $steps,
        ];
    }

    protected function activeIndexForStatus(string $status): int
    {
        return match ($status) {
            'paid', 'pending' => 0,
            'being_prepared' => 1,
            'in_production', 'processing' => 2,
            'ready_to_pack' => 3,
            'dispatched', 'shipped' => 4,
            'delivered' => 5,
            default => 0,
        };
    }

    protected function orderDescription(Order $order): string
    {
        $item = $order->items->first();
        if (! $item) {
            return 'Order';
        }

        $qty = $order->items->sum('quantity');

        return $item->product_title.' × '.$qty;
    }

    protected function etaLabel(Order $order): string
    {
        if ($order->status === 'delivered') {
            $date = $order->estimated_delivery_at ?? $order->updated_at;

            return 'Delivered on '.$date->format('d M Y');
        }

        if ($order->estimated_delivery_at) {
            return $order->estimated_delivery_at->format('l, d M Y');
        }

        return 'To be confirmed by our team';
    }

    public function categoryLabel(?string $slug): string
    {
        if (! $slug) {
            return 'General';
        }

        return Str::title(str_replace('-', ' ', $slug));
    }

    public function productsSummary(Order $order): string
    {
        $items = $order->items;
        if ($items->isEmpty()) {
            return '—';
        }

        $first = $items->first();
        $qty = $items->sum('quantity');

        if ($items->count() === 1) {
            return $first->product_title.' × '.$qty;
        }

        return $first->product_title.' +'.($items->count() - 1).' more';
    }

    public function productsLabel(Order $order): string
    {
        $items = $order->items;
        if ($items->isEmpty()) {
            return '—';
        }

        if ($items->count() === 1) {
            return $items->first()->product_title;
        }

        return $items->first()->product_title.' +'.($items->count() - 1).' more';
    }

    public function toOrderDetailPayload(Order $order): array
    {
        $items = $order->items->map(function (OrderItem $item) {
            $hasDesign = ! empty($item->design_data);

            return [
                'item_id' => $item->id,
                'has_design' => $hasDesign,
                'design' => $hasDesign ? $this->toDesignPreviewPayload($item) : null,
                'product_title' => $item->product_title,
                'category' => $this->categoryLabel($item->category_slug),
                'colour' => $item->colour_name,
                'size' => $item->size,
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->unit_price, 2),
                'line_total' => number_format($item->line_total, 2),
            ];
        });

        $shippingParts = array_filter([
            $order->shipping_address_line1,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_zip,
        ]);

        return [
            'order_number' => $order->order_number,
            'date' => $order->created_at->format('d M Y'),
            'date_full' => $order->created_at->format('d M Y, H:i'),
            'status' => \App\Support\OrderFulfillmentStatus::label($order->status),
            'status_label' => $order->displayStatus(),
            'items' => $items->values()->all(),
            'total_qty' => $order->items->sum('quantity'),
            'subtotal' => number_format($order->subtotal, 2),
            'vat_amount' => number_format($order->vat_amount, 2),
            'total' => number_format($order->total, 2),
            'shipping_address' => implode(', ', $shippingParts) ?: '—',
            'name' => $order->name,
            'email' => $order->email,
            'phone' => $order->phone ?? '—',
            'company' => $order->company_name,
            'instructions' => $order->additional_instructions,
            'eta' => $this->etaLabel($order),
        ];
    }

    public function toDesignPreviewPayload(OrderItem $item): array
    {
        $data = $item->design_data ?? [];

        return [
            'title' => $item->product_title,
            'colour_hex' => $data['colour']['hex'] ?? '#ffffff',
            'colour_name' => $item->colour_name ?? ($data['colour']['name'] ?? ''),
            'customizer_image' => $data['customizer_image'] ?? asset('assets/images/customizer/tshirtcustom.png'),
            'customizer_image_back' => $data['customizer_image_back'] ?? asset('assets/images/customizer/tshirtcustom-back.png'),
            'size_breakdown' => $item->size_breakdown,
            'quantity' => $item->quantity,
            'design_data' => $data,
            'storage_base' => asset('storage'),
            'preview_url' => DesignPreview::resolveUrl(
                $item->design_file_path,
                $item->design_assets ?? null,
                $data
            ),
        ];
    }
}
