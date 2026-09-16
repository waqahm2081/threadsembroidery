<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBulkEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductBulkEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $enquiries = ProductBulkEnquiry::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('reference', 'like', "%{$q}%")
                        ->orWhere('full_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('product_title', 'like', "%{$q}%")
                        ->orWhere('product_style_code', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.bulk-enquiries.index', [
            'title' => 'Bulk Product Enquiries',
            'subtitle' => 'Enquiries for quantities over 100 units.',
            'nav' => 'bulk-enquiries',
            'enquiries' => $enquiries,
            'q' => $q,
            'status' => $status,
            'statuses' => ['submitted', 'review', 'quoted', 'closed'],
        ]);
    }

    public function show(ProductBulkEnquiry $bulkEnquiry): View
    {
        return view('admin.bulk-enquiries.show', [
            'title' => $bulkEnquiry->reference,
            'subtitle' => $bulkEnquiry->product_title,
            'nav' => 'bulk-enquiries',
            'enquiry' => $bulkEnquiry,
            'statuses' => ['submitted', 'review', 'quoted', 'closed'],
        ]);
    }

    public function update(Request $request, ProductBulkEnquiry $bulkEnquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:submitted,review,quoted,closed'],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $bulkEnquiry->update($validated);

        return back()->with('success', 'Enquiry updated.');
    }
}
