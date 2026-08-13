<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BrandingEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BrandingEnquiryController extends Controller
{
    public const STATUSES = [
        'submitted',
        'review',
        'quoted',
        'in_progress',
        'completed',
        'cancelled',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');

        $enquiries = BrandingEnquiry::query()
            ->with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('reference', 'like', "%{$q}%")
                        ->orWhere('full_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%");
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'title' => 'Branding Enquiries',
            'subtitle' => 'Review custom branding requests.',
            'nav' => 'enquiries',
            'enquiries' => $enquiries,
            'q' => $q,
            'status' => $status,
            'statuses' => self::STATUSES,
        ]);
    }

    public function show(BrandingEnquiry $enquiry): View
    {
        $enquiry->load(['files', 'user']);

        return view('admin.enquiries.show', [
            'title' => $enquiry->reference,
            'subtitle' => $enquiry->enquiry_type ?: 'Branding enquiry',
            'nav' => 'enquiries',
            'enquiry' => $enquiry,
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, BrandingEnquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
            'admin_notes' => ['nullable', 'string', 'max:5000'],
            'quoted_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $enquiry->status = $validated['status'];
        $enquiry->admin_notes = $validated['admin_notes'] ?? null;
        $enquiry->quoted_amount = $validated['quoted_amount'] ?? null;
        $enquiry->save();

        return back()->with('success', 'Enquiry updated successfully.');
    }
}
