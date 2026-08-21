<?php

namespace App\Mail;

use App\Models\SchoolUniformOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UniformOrderMail extends SiteMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SchoolUniformOrder $order,
        public bool $isAdmin = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? 'New uniform order '.$this->order->order_number
                : 'Uniform order confirmation '.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.uniform-order',
            with: $this->sharedViewData([
                'isAdmin' => $this->isAdmin,
                'actionUrl' => $this->isAdmin
                    ? route('admin.school-uniform-orders.show', $this->order)
                    : route('portal'),
                'subject' => $this->envelope()->subject,
            ]),
        );
    }
}
