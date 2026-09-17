<?php

namespace App\Mail;

use App\Support\OrderFulfillmentStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateMail extends SiteMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $orderTypeLabel,
        public string $orderNumber,
        public string $customerName,
        public string $status,
        public ?string $trackingNumber = null,
        public ?string $actionUrl = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order update '.$this->orderNumber.' — '.OrderFulfillmentStatus::label($this->status),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-update',
            with: $this->sharedViewData([
                'statusLabel' => OrderFulfillmentStatus::label($this->status),
                'subject' => $this->envelope()->subject,
            ]),
        );
    }
}
