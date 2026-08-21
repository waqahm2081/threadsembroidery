<?php

namespace App\Services;

use App\Mail\BundleOrderMail;
use App\Mail\UniformOrderMail;
use App\Mail\UserRegisteredMail;
use App\Models\Order;
use App\Models\SchoolUniformOrder;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationMailService
{
    /**
     * @return list<string>
     */
    public function adminEmails(): array
    {
        $email = strtolower(trim((string) config('mail.admin_email')));

        return $email !== '' ? [$email] : [];
    }

    public function sendUserRegistered(User $user): void
    {
        $this->safeSend(fn () => Mail::to($user->email)->send(new UserRegisteredMail($user, false)));

        foreach ($this->adminEmails() as $email) {
            if (strcasecmp($email, $user->email) === 0) {
                continue;
            }
            $this->safeSend(fn () => Mail::to($email)->send(new UserRegisteredMail($user, true)));
        }
    }

    public function sendBundleOrder(Order $order): void
    {
        $order->loadMissing('items');

        if ($order->email) {
            $this->safeSend(fn () => Mail::to($order->email)->send(new BundleOrderMail($order, false)));
        }

        foreach ($this->adminEmails() as $email) {
            if ($order->email && strcasecmp($email, $order->email) === 0) {
                continue;
            }
            $this->safeSend(fn () => Mail::to($email)->send(new BundleOrderMail($order, true)));
        }
    }

    public function sendUniformOrder(SchoolUniformOrder $order): void
    {
        $order->loadMissing(['items', 'school']);

        if ($order->customer_email) {
            $this->safeSend(fn () => Mail::to($order->customer_email)->send(new UniformOrderMail($order, false)));
        }

        foreach ($this->adminEmails() as $email) {
            if ($order->customer_email && strcasecmp($email, $order->customer_email) === 0) {
                continue;
            }
            $this->safeSend(fn () => Mail::to($email)->send(new UniformOrderMail($order, true)));
        }
    }

    private function safeSend(callable $callback): void
    {
        try {
            $callback();
        } catch (\Throwable $e) {
            Log::error('Email notification failed: '.$e->getMessage(), [
                'exception' => $e::class,
                'file' => $e->getFile().':'.$e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
