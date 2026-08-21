<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolUniformOrder;
use Illuminate\Http\Request;

class SchoolUniformOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolUniformOrder::with(['school', 'user'])->withCount('items');

        if ($schoolId = $request->get('school_id')) {
            $query->where('school_id', $schoolId);
        }
        if ($status = $request->get('status')) {
            $query->where('status', $status);
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
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        return view('admin.schools.orders.index', compact('orders', 'schools', 'statuses'));
    }

    public function show(SchoolUniformOrder $schoolUniformOrder)
    {
        $schoolUniformOrder->load(['school', 'user', 'items.product', 'items.variant']);

        return view('admin.schools.orders.show', ['order' => $schoolUniformOrder]);
    }

    public function updateStatus(Request $request, SchoolUniformOrder $schoolUniformOrder)
    {
        $request->validate(['status' => ['required', 'in:pending,processing,shipped,delivered,cancelled']]);
        $schoolUniformOrder->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }
}
