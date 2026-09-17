<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationMailService;
use App\Support\OrderFulfillmentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');
        $statuses = OrderFulfillmentStatus::values();

        $orders = Order::query()
            ->with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('order_number', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('name', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, $statuses, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', [
            'title' => 'Orders',
            'subtitle' => 'View and update bundle orders.',
            'nav' => 'orders',
            'orders' => $orders,
            'q' => $q,
            'status' => $status,
            'statuses' => OrderFulfillmentStatus::labels(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items.files', 'user']);

        return view('admin.orders.show', [
            'title' => 'Order '.$order->order_number,
            'subtitle' => 'Placed '.optional($order->created_at)->format('d M Y H:i'),
            'nav' => 'orders',
            'order' => $order,
            'statuses' => OrderFulfillmentStatus::labels(),
        ]);
    }

    public function updateStatus(Request $request, Order $order, NotificationMailService $mail): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(OrderFulfillmentStatus::values())],
            'tracking_number' => [
                Rule::requiredIf(fn () => $request->input('status') === OrderFulfillmentStatus::DISPATCHED),
                'nullable',
                'string',
                'max:120',
            ],
        ]);

        $order->status = $validated['status'];
        $tracking = trim((string) ($validated['tracking_number'] ?? ''));
        $order->tracking_number = $tracking !== '' ? $tracking : null;
        $order->save();

        $mail->sendBundleOrderStatusUpdate($order);

        return back()->with(
            'success',
            'Order status updated to '.OrderFulfillmentStatus::label($order->status).'. Customer notified by email.'
        );
    }
}
