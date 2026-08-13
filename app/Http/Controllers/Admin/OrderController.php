<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public const STATUSES = [
        'pending',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

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
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
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
            'statuses' => self::STATUSES,
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
            'statuses' => self::STATUSES,
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $order->status = $validated['status'];
        $order->save();

        return back()->with('success', 'Order status updated to '.$validated['status'].'.');
    }
}
