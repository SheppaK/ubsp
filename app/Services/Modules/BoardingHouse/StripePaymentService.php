<?php

namespace App\Services\Modules\BoardingHouse;

use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Payment;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePaymentService
{
    public function isConfigured(): bool
    {
        return filled(config('boarding-house.stripe.secret'));
    }

    public function createCheckoutSession(BookingRequest $booking): ?Payment
    {
        if (! $this->isConfigured()) {
            Log::info('Stripe not configured — skipping checkout for booking '.$booking->id);

            return null;
        }

        Stripe::setApiKey(config('boarding-house.stripe.secret'));

        $amount = config('boarding-house.holding_fee.amount');
        $currency = config('boarding-house.holding_fee.currency');

        $payment = Payment::create([
            'booking_request_id' => $booking->id,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Holding Fee — '.$booking->room->property->name,
                        'description' => 'Room: '.$booking->room->name,
                    ],
                    'unit_amount' => (int) ($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('modules.boarding-house.payments.success', $payment).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('modules.boarding-house.bookings.mine'),
            'metadata' => [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
            ],
        ]);

        $payment->update(['stripe_session_id' => $session->id]);
        $booking->update(['payment_status' => 'pending']);

        return $payment;
    }

    public function verifySession(Payment $payment, string $sessionId): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        Stripe::setApiKey(config('boarding-house.stripe.secret'));
        $session = Session::retrieve($sessionId);

        if ($session->payment_status === 'paid') {
            $payment->update([
                'status' => 'paid',
                'stripe_payment_intent' => $session->payment_intent,
                'paid_at' => now(),
            ]);
            $payment->bookingRequest->update(['payment_status' => 'paid']);

            return true;
        }

        return false;
    }

    public function checkoutUrl(Payment $payment): ?string
    {
        if (! $payment->stripe_session_id || ! $this->isConfigured()) {
            return null;
        }

        Stripe::setApiKey(config('boarding-house.stripe.secret'));

        return Session::retrieve($payment->stripe_session_id)->url;
    }
}
