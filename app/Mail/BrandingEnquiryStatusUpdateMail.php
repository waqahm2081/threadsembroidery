<?php

namespace App\Mail;

use App\Models\BrandingEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BrandingEnquiryStatusUpdateMail extends SiteMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BrandingEnquiry $enquiry,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Enquiry update '.$this->enquiry->reference.' — '.$this->enquiry->displayStatus(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.branding-enquiry-status',
            with: $this->sharedViewData([
                'enquiry' => $this->enquiry,
                'statusLabel' => $this->enquiry->displayStatus(),
                'portalUrl' => route('portal'),
                'subject' => $this->envelope()->subject,
            ]),
        );
    }
}
