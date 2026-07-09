<?php

namespace App\Http\Controllers;

use App\Models\BrandingEnquiry;
use App\Models\BrandingEnquiryFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandingEnquiryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'min_order_confirm' => ['accepted'],
            'full_name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'garments' => ['required', 'string', 'min:5', 'max:5000'],
            'colours' => ['required', 'string', 'max:255'],
            'placements' => ['required', 'array', 'min:1'],
            'placements.*' => ['string', 'in:Left Chest,Centre Chest,Right Chest,Large Back,Sleeve'],
            'branding_method' => ['required', 'in:embroidery,dtf,both,not_sure'],
            'design_files' => ['required', 'array', 'min:1'],
            'design_files.*' => [
                'file',
                'max:10240',
                'mimes:png,jpg,jpeg,svg,pdf,ai,eps,psd',
            ],
        ], [
            'min_order_confirm.accepted' => 'Please confirm you meet the minimum order quantity.',
            'design_files.required' => 'Please upload at least one design file.',
            'design_files.min' => 'Please upload at least one design file.',
            'placements.required' => 'Please select at least one placement.',
            'placements.min' => 'Please select at least one placement.',
        ]);

        $enquiry = DB::transaction(function () use ($validated, $request) {
            $enquiry = BrandingEnquiry::create([
                'reference' => BrandingEnquiry::generateReference(),
                'user_id' => auth()->id(),
                'full_name' => $validated['full_name'],
                'business_name' => $validated['business_name'],
                'email' => $validated['email'],
                'whatsapp' => $validated['whatsapp'],
                'garments' => $validated['garments'],
                'colours' => $validated['colours'],
                'placements' => $validated['placements'],
                'branding_method' => $validated['branding_method'],
                'min_order_confirmed' => true,
                'status' => 'submitted',
            ]);

            foreach ($request->file('design_files', []) as $file) {
                $path = $file->store("branding-enquiries/{$enquiry->id}", 'public');

                BrandingEnquiryFile::create([
                    'branding_enquiry_id' => $enquiry->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }

            return $enquiry;
        });

        return response()->json([
            'success' => true,
            'message' => 'Your branding enquiry has been submitted successfully.',
            'reference' => $enquiry->reference,
            'dashboard_url' => auth()->check() ? route('user-dashboard') : null,
        ], 201);
    }
}
