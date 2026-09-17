<?php

namespace App\Http\Controllers;

use App\Models\BrandingEnquiry;
use App\Models\BrandingEnquiryFile;
use App\Services\NotificationMailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BrandingEnquiryController extends Controller
{
    public function __construct(private NotificationMailService $mailNotifications)
    {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:190'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:40'],
            'enquiry_type' => ['required', 'string', 'max:190'],
            'quantity' => ['nullable', 'string', 'max:80'],
            'method' => ['nullable', 'string', 'max:80'],
            'placement' => ['nullable', 'array'],
            'placement.*' => ['string', 'max:80'],
            'message' => ['required', 'string', 'max:5000'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'max:10240', 'mimes:png,jpg,jpeg,svg,pdf'],
        ]);

        $enquiry = DB::transaction(function () use ($validated, $request) {
            $enquiry = BrandingEnquiry::create([
                'reference' => BrandingEnquiry::generateReference(),
                'user_id' => Auth::id(),
                'full_name' => $validated['name'],
                'company_name' => $validated['company'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'enquiry_type' => $validated['enquiry_type'],
                'quantity' => $validated['quantity'] ?? null,
                'branding_method' => $validated['method'] ?? null,
                'placements' => array_values($validated['placement'] ?? []),
                'message' => $validated['message'],
                'status' => 'submitted',
            ]);

            foreach ($request->file('files', []) as $file) {
                if (! $file) {
                    continue;
                }

                $path = $file->store('branding-enquiries/'.$enquiry->id, 'public');

                BrandingEnquiryFile::create([
                    'branding_enquiry_id' => $enquiry->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }

            return $enquiry->load('files');
        });

        $this->mailNotifications->sendBrandingEnquiry($enquiry);

        return response()->json([
            'ok' => true,
            'message' => 'Your branding enquiry has been submitted successfully.',
            'reference' => $enquiry->reference,
            'portal_url' => Auth::check() ? route('portal') : null,
        ], 201);
    }
}
