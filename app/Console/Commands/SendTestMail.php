<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestMail extends Command
{
    protected $signature = 'mail:test {email? : Destination address (defaults to ADMIN_EMAIL)}';

    protected $description = 'Send a plain test email to verify SMTP settings';

    public function handle(): int
    {
        $to = $this->argument('email') ?: config('mail.admin_email');

        if (! $to) {
            $this->error('No email given and ADMIN_EMAIL is empty.');

            return self::FAILURE;
        }

        $this->info('Mailer: '.config('mail.default'));
        $this->info('Host: '.config('mail.mailers.smtp.host').':'.config('mail.mailers.smtp.port'));
        $this->info('Encryption: '.(config('mail.mailers.smtp.encryption') ?: '(none)'));
        $this->info('From: '.config('mail.from.address'));
        $this->info('Sending to: '.$to);

        try {
            Mail::raw('Threads Embroidery — SMTP test at '.now()->toDateTimeString(), function ($message) use ($to) {
                $message->to($to)->subject('Threads mail test');
            });
            $this->info('Sent successfully (check inbox/spam).');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Send failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
