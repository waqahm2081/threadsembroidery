<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRegisteredMail extends SiteMailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public bool $isAdmin = false,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isAdmin
                ? 'New user registered — '.$this->user->name
                : 'Welcome to '.config('site.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->isAdmin ? 'emails.user-registered-admin' : 'emails.user-registered',
            with: $this->sharedViewData([
                'portalUrl' => route('portal'),
                'adminUsersUrl' => route('admin.users.index'),
                'subject' => $this->envelope()->subject,
            ]),
        );
    }
}
