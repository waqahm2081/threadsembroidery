<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class StripePaymentService
{
    public function publishableKey(): string
    {
        return (string) config('services.stripe.key');
    }

    public function currency(): string
    {
        return strtolower((string) config('services.stripe.currency', 'gbp'));
    }

    public function configured(): bool
    {
        return $this->publishableKey() !== '' && (string) config('services.stripe.secret') !== '';
    }

    private function boot(): void
    {
        $secret = (string) config('services.stripe.secret');
        if ($secret === '') {
            throw ValidationException::withMessages([
                'payment' => 'Stripe is not configured. Please add STRIPE_SECRETKEY to the environment.',
            ]);
        }

        Stripe::setApiKey($secret);
    }

    public function toMinorUnits(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * @param  array<string, string>  $metadata
     * @return array{id:string,client_secret:string,amount:int,currency:string}
     */
    public function createPaymentIntent(float $amountMajor, array $metadata = [], ?string $receiptEmail = null): array
    {
        $this->boot();

        $amount = $this->toMinorUnits($amountMajor);
        if ($amount < 30) {
            throw ValidationException::withMessages([
                'payment' => 'Order total is too low to charge.',
            ]);
        }

        try {
            $payload = [
                'amount' => $amount,
                'currency' => $this->currency(),
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => $metadata,
            ];
            if ($receiptEmail) {
                $payload['receipt_email'] = $receiptEmail;
            }

            $intent = PaymentIntent::create($payload);
        } catch (ApiErrorException $e) {
            throw ValidationException::withMessages([
                'payment' => $e->getMessage() ?: 'Could not start payment.',
            ]);
        }

        return [
            'id' => $intent->id,
            'client_secret' => (string) $intent->client_secret,
            'amount' => (int) $intent->amount,
            'currency' => (string) $intent->currency,
        ];
    }

    public function assertPaid(string $paymentIntentId, float $expectedAmountMajor): PaymentIntent
    {
        $this->boot();

        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw ValidationException::withMessages([
                'payment' => 'Could not verify payment with Stripe.',
            ]);
        }

        if (($intent->status ?? '') !== 'succeeded') {
            throw ValidationException::withMessages([
                'payment' => 'Payment has not been completed. Please try again.',
            ]);
        }

        $expected = $this->toMinorUnits($expectedAmountMajor);
        if ((int) $intent->amount !== $expected) {
            throw ValidationException::withMessages([
                'payment' => 'Paid amount does not match the order total.',
            ]);
        }

        if (strtolower((string) $intent->currency) !== $this->currency()) {
            throw ValidationException::withMessages([
                'payment' => 'Payment currency mismatch.',
            ]);
        }

        return $intent;
    }
}
