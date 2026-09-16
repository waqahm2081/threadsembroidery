<?php

namespace App\Mail;

use App\Models\ProductBulkEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductBulkEnquiryMail extends SiteMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ProductBulkEnquiry $enquiry,
        public bool $isAdmin = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? 'Bulk product enquiry — '.$this->enquiry->reference
                : 'We received your bulk enquiry — '.$this->enquiry->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->isAdmin
                ? 'emails.product-bulk-enquiry-admin'
                : 'emails.product-bulk-enquiry',
            with: $this->sharedViewData([
                'enquiry' => $this->enquiry,
                'adminUrl' => route('admin.bulk-enquiries.show', $this->enquiry),
                'subject' => $this->envelope()->subject,
            ]),
        );
    }
}
