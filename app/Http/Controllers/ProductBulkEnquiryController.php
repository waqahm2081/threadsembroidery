<?php

namespace App\Http\Controllers;

use App\Models\ProductBulkEnquiry;
use App\Services\NotificationMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductBulkEnquiryController extends Controller
{
    public function __construct(
        private NotificationMailService $mailNotifications,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:60'],
            'company_name' => ['nullable', 'string', 'max:190'],
            'message' => ['nullable', 'string', 'max:5000'],
            'product_title' => ['required', 'string', 'max:255'],
            'product_style_code' => ['nullable', 'string', 'max:100'],
            'category_slug' => ['nullable', 'string', 'max:120'],
            'product_slug' => ['nullable', 'string', 'max:190'],
            'colour_name' => ['nullable', 'string', 'max:100'],
            'colour_code' => ['nullable', 'string', 'max:40'],
            'colour_hex' => ['nullable', 'string', 'max:20'],
            'size_breakdown' => ['required', 'array', 'min:1'],
            'size_breakdown.*.size' => ['required', 'string', 'max:40'],
            'size_breakdown.*.qty' => ['required', 'integer', 'min:1'],
            'size_breakdown.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:101'],
            'decoration_methods' => ['nullable', 'array'],
            'decoration_methods.*' => ['string', 'in:print,embroidery'],
            'logo_setup' => ['nullable', 'boolean'],
            'estimated_unit_price' => ['nullable', 'numeric', 'min:0'],
            'estimated_subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $totalQty = collect($validated['size_breakdown'])->sum(fn ($r) => (int) ($r['qty'] ?? 0));
        if ($totalQty < 101) {
            return response()->json([
                'message' => 'Bulk enquiries require a total quantity above 100.',
            ], 422);
        }

        $enquiry = ProductBulkEnquiry::create([
            'reference' => ProductBulkEnquiry::generateReference(),
            'user_id' => $request->user()?->id,
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'company_name' => $validated['company_name'] ?? null,
            'product_title' => $validated['product_title'],
            'product_style_code' => $validated['product_style_code'] ?? null,
            'category_slug' => $validated['category_slug'] ?? null,
            'product_slug' => $validated['product_slug'] ?? null,
            'colour_name' => $validated['colour_name'] ?? null,
            'colour_code' => $validated['colour_code'] ?? null,
            'colour_hex' => $validated['colour_hex'] ?? null,
            'size_breakdown' => $validated['size_breakdown'],
            'quantity' => $totalQty,
            'decoration_methods' => $validated['decoration_methods'] ?? [],
            'logo_setup' => (bool) ($validated['logo_setup'] ?? false),
            'estimated_unit_price' => $validated['estimated_unit_price'] ?? null,
            'estimated_subtotal' => $validated['estimated_subtotal'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'submitted',
        ]);

        $this->mailNotifications->sendProductBulkEnquiry($enquiry);

        return response()->json([
            'message' => 'Your enquiry has been submitted. We will get back to you shortly.',
            'reference' => $enquiry->reference,
        ]);
    }
}
