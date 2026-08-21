<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BundleOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public bool $isAdmin = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? 'New bundle order '.$this->order->order_number
                : 'Order confirmation '.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bundle-order',
            with: [
                'logoUrl' => asset('assets/img/logo.png'),
                'isAdmin' => $this->isAdmin,
                'actionUrl' => $this->isAdmin
                    ? route('admin.orders.show', $this->order)
                    : route('portal'),
                'subject' => $this->envelope()->subject,
            ],
        );
    }
}
