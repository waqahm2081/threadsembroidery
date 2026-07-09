<?php

namespace App\Http\Controllers;

use App\Models\BrandingEnquiry;
use App\Models\Order;
use App\Services\OrderTrackingService;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function __construct(private OrderTrackingService $trackingService)
    {
    }

    public function index(): View
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with(['items', 'trackingEvents'])
            ->latest()
            ->get();

        $brandingEnquiries = BrandingEnquiry::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->latest()
            ->get();

        $stats = [
            'total_orders' => $orders->count(),
            'total_spent' => (float) $orders->sum('total'),
            'active_orders' => $orders->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            'completed' => $orders->where('status', 'delivered')->count(),
            'branding_enquiries' => $brandingEnquiries->count(),
        ];

        $recentOrders = $orders->take(3);

        $trackingData = $orders->mapWithKeys(function (Order $order) {
            return [$order->order_number => $this->trackingService->toTrackingPayload($order)];
        });

        $orderDetailsData = $orders->mapWithKeys(function (Order $order) {
            return [$order->order_number => $this->trackingService->toOrderDetailPayload($order)];
        });

        $nameParts = explode(' ', $user->name, 2);

        return view('user-dashboard', [
            'user' => $user,
            'firstName' => $nameParts[0] ?? '',
            'lastName' => $nameParts[1] ?? '',
            'stats' => $stats,
            'orders' => $orders,
            'recentOrders' => $recentOrders,
            'brandingEnquiries' => $brandingEnquiries,
            'trackingData' => $trackingData,
            'orderDetailsData' => $orderDetailsData,
            'trackingService' => $this->trackingService,
        ]);
    }
}
