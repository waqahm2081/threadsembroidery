<?php

namespace App\Mail;

use App\Models\BrandingEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrandingEnquiryMail extends SiteMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BrandingEnquiry $enquiry,
        public bool $isAdmin = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? 'New branding enquiry '.$this->enquiry->reference
                : 'We received your branding enquiry '.$this->enquiry->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->isAdmin
                ? 'emails.branding-enquiry-admin'
                : 'emails.branding-enquiry',
            with: $this->sharedViewData([
                'enquiry' => $this->enquiry,
                'adminUrl' => route('admin.enquiries.show', $this->enquiry),
                'portalUrl' => route('portal'),
                'subject' => $this->envelope()->subject,
            ]),
        );
    }
}
