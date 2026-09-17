<?php

namespace App\Services;

use App\Mail\BundleOrderMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\ProductBulkEnquiryMail;
use App\Mail\UniformOrderMail;
use App\Mail\UserRegisteredMail;
use App\Models\Order;
use App\Models\ProductBulkEnquiry;
use App\Models\SchoolUniformOrder;
use App\Models\ShopOrder;
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

    public function sendProductBulkEnquiry(ProductBulkEnquiry $enquiry): void
    {
        if ($enquiry->email) {
            $this->safeSend(fn () => Mail::to($enquiry->email)->send(new ProductBulkEnquiryMail($enquiry, false)));
        }

        foreach ($this->adminEmails() as $email) {
            if ($enquiry->email && strcasecmp($email, $enquiry->email) === 0) {
                continue;
            }
            $this->safeSend(fn () => Mail::to($email)->send(new ProductBulkEnquiryMail($enquiry, true)));
        }
    }

    public function sendBundleOrderStatusUpdate(Order $order): void
    {
        if (! $order->email) {
            return;
        }

        $this->safeSend(fn () => Mail::to($order->email)->send(new OrderStatusUpdateMail(
            orderTypeLabel: 'Bundle',
            orderNumber: (string) $order->order_number,
            customerName: (string) $order->name,
            status: (string) $order->status,
            trackingNumber: $order->tracking_number,
            actionUrl: route('portal'),
        )));
    }

    public function sendShopOrderStatusUpdate(ShopOrder $order): void
    {
        if (! $order->email) {
            return;
        }

        $this->safeSend(fn () => Mail::to($order->email)->send(new OrderStatusUpdateMail(
            orderTypeLabel: 'Shop',
            orderNumber: (string) $order->order_number,
            customerName: (string) $order->name,
            status: (string) $order->status,
            trackingNumber: $order->tracking_number,
            actionUrl: route('portal'),
        )));
    }

    public function sendUniformOrderStatusUpdate(SchoolUniformOrder $order): void
    {
        if (! $order->customer_email) {
            return;
        }

        $this->safeSend(fn () => Mail::to($order->customer_email)->send(new OrderStatusUpdateMail(
            orderTypeLabel: 'Uniform',
            orderNumber: (string) $order->order_number,
            customerName: (string) $order->customer_name,
            status: (string) $order->status,
            trackingNumber: $order->tracking_number,
            actionUrl: route('portal'),
        )));
    }

    private function safeSend(callable $callback): void
    {
        try {
            $callback();
            Log::info('Email notification sent successfully.');
        } catch (\Throwable $e) {
            Log::error('Email notification failed: '.$e->getMessage(), [
                'exception' => $e::class,
                'file' => $e->getFile().':'.$e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
