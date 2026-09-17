<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolUniformOrder;
use App\Services\NotificationMailService;
use App\Support\OrderFulfillmentStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolUniformOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = SchoolUniformOrder::with(['school', 'user'])->withCount('items');

        if ($schoolId = $request->get('school_id')) {
            $query->where('school_id', $schoolId);
        }
        if ($status = $request->get('status')) {
            if (in_array($status, OrderFulfillmentStatus::values(), true)) {
                $query->where('status', $status);
            }
        }
        if ($q = $request->get('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%");
            });
        }

        $orders = $query->latest()->paginate(25)->withQueryString();
        $schools = School::orderBy('name')->get();
        $statuses = OrderFulfillmentStatus::labels();

        return view('admin.schools.orders.index', [
            'title' => 'Uniform Orders',
            'subtitle' => 'School uniform shop orders.',
            'nav' => 'school-uniform-orders',
            'orders' => $orders,
            'schools' => $schools,
            'statuses' => $statuses,
            'status' => $request->get('status'),
            'q' => $request->get('q'),
            'school_id' => $request->get('school_id'),
        ]);
    }

    public function show(SchoolUniformOrder $schoolUniformOrder): View
    {
        $schoolUniformOrder->load(['school', 'user', 'items.product', 'items.variant']);

        return view('admin.schools.orders.show', [
            'title' => 'Uniform Order '.$schoolUniformOrder->order_number,
            'subtitle' => 'Placed '.optional($schoolUniformOrder->created_at)->format('d M Y H:i'),
            'nav' => 'school-uniform-orders',
            'order' => $schoolUniformOrder,
            'statuses' => OrderFulfillmentStatus::labels(),
        ]);
    }

    public function updateStatus(Request $request, SchoolUniformOrder $schoolUniformOrder, NotificationMailService $mail): RedirectResponse
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

        $tracking = trim((string) ($validated['tracking_number'] ?? ''));
        $schoolUniformOrder->update([
            'status' => $validated['status'],
            'tracking_number' => $tracking !== '' ? $tracking : null,
        ]);

        $mail->sendUniformOrderStatusUpdate($schoolUniformOrder);

        return back()->with(
            'success',
            'Order status updated to '.OrderFulfillmentStatus::label($schoolUniformOrder->status).'. Customer notified by email.'
        );
    }
}
