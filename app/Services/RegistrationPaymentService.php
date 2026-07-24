<?php

namespace App\Services;

use App\Models\Business;
use App\Models\RegistrationPayment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegistrationPaymentService
{
    public function __construct(
        protected BusinessRegistrationService $registration,
        protected KcpayService $kcpay,
    ) {}

    /**
     * Create unpaid business account + pending payment record.
     *
     * @param  array<string, mixed>  $validated
     */
    public function createPendingPayment(array $validated): RegistrationPayment
    {
        $modules = $validated['modules'];
        $total = $this->kcpay->calculateTotal($modules);

        $payload = [
            'business_name' => $validated['business_name'],
            'owner_name' => $validated['owner_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'password' => $validated['password'],
            'modules' => $modules,
        ];

        return DB::transaction(function () use ($validated, $modules, $total, $payload) {
            $business = $this->registration->registerUnpaid($validated, $modules);

            $payment = RegistrationPayment::create([
                'seller_reference' => RegistrationPayment::generateReference(),
                'amount_zmw' => $total,
                'currency' => 'ZMW',
                'status' => $total <= 0 ? 'paid' : 'pending',
                'modules' => $modules,
                'registration_payload' => RegistrationPayment::encryptPayload($payload),
                'business_id' => $business->id,
                'user_id' => $business->owner_id,
                'paid_at' => $total <= 0 ? now() : null,
            ]);

            if ($total <= 0) {
                $this->unlockBusinessModules($payment->fresh());
            }

            return $payment->fresh(['business', 'user']);
        });
    }

    /**
     * Find the open (unpaid) payment for a business, or create one from selected modules.
     */
    public function pendingPaymentForBusiness(Business $business): ?RegistrationPayment
    {
        return RegistrationPayment::query()
            ->where('business_id', $business->id)
            ->whereIn('status', ['pending', 'processing', 'failed'])
            ->latest()
            ->first();
    }

    public function fulfillIfPaid(RegistrationPayment $payment): ?Business
    {
        if (! $payment->isPaid()) {
            return null;
        }

        return DB::transaction(function () use ($payment) {
            $payment = RegistrationPayment::query()->lockForUpdate()->findOrFail($payment->id);

            if ($payment->business_id) {
                return $this->unlockBusinessModules($payment);
            }

            $data = $payment->getRegistrationData();

            if ($data === []) {
                throw new \RuntimeException('Registration data is missing for payment '.$payment->seller_reference);
            }

            $business = $this->registration->register($data, $data['modules'] ?? []);

            $payment->update([
                'business_id' => $business->id,
                'user_id' => $business->owner_id,
            ]);

            return $business->load('owner');
        });
    }

    protected function unlockBusinessModules(RegistrationPayment $payment): Business
    {
        $business = $payment->business()->lockForUpdate()->firstOrFail();
        $user = $payment->user ?? $business->owner;

        if ($business->hasPaid() && $business->modules()->exists()) {
            return $business->load('owner');
        }

        $modules = $payment->modules ?? [];

        if ($modules === []) {
            $data = $payment->getRegistrationData();
            $modules = $data['modules'] ?? [];
        }

        $this->registration->activateModules($business, $modules, $user);
        $business->markPaid();

        if (! $payment->isPaid()) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        }

        return $business->fresh()->load('owner');
    }

    /**
     * @param  array<string, mixed>  $callbackBody
     */
    public function handleCallback(array $callbackBody): bool
    {
        $reference = $callbackBody['sellerReference'] ?? null;

        if (! $reference) {
            return false;
        }

        $payment = RegistrationPayment::query()->where('seller_reference', $reference)->first();

        if (! $payment) {
            Log::warning('[KC Pay] Callback for unknown reference', ['reference' => $reference]);

            return false;
        }

        return $this->applyGatewayStatus($payment, $callbackBody['status'] ?? '', $callbackBody);
    }

    public function syncFromGateway(RegistrationPayment $payment): RegistrationPayment
    {
        if ($payment->isPaid() && $payment->business?->hasPaid()) {
            return $payment;
        }

        $result = $this->kcpay->checkStatus($payment);

        if ($result['success'] ?? false) {
            $this->applyGatewayStatus($payment, $result['status'] ?? 'pending', $result['raw'] ?? []);
        }

        return $payment->fresh();
    }

    /**
     * Docs: sellerReference must be unique per transaction — refresh before each new initiate/retry.
     */
    public function refreshSellerReference(RegistrationPayment $payment): RegistrationPayment
    {
        if ($payment->isPaid()) {
            return $payment;
        }

        $payment->update([
            'seller_reference' => RegistrationPayment::generateReference(),
            'token' => null,
            'transaction_id' => null,
            'status' => 'pending',
            'failed_at' => null,
            'kcpay_init_response' => null,
            'kcpay_callback_payload' => null,
        ]);

        return $payment->fresh();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function applyGatewayStatus(RegistrationPayment $payment, ?string $status, array $payload): bool
    {
        if ($payment->isPaid() && $payment->business?->hasPaid()) {
            return true;
        }

        $payment->update([
            'kcpay_callback_payload' => $payload ?: $payment->kcpay_callback_payload,
            'transaction_id' => $payload['transactionId'] ?? $payment->transaction_id,
            'token' => $payload['token'] ?? $payment->token,
        ]);

        if ($this->kcpay->isSuccessStatus($status)) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'failed_at' => null,
            ]);

            $this->fulfillIfPaid($payment->fresh());

            return true;
        }

        if ($this->kcpay->isFailedStatus($status)) {
            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
            ]);

            return true;
        }

        $payment->update(['status' => 'processing']);

        return true;
    }

    public function loginRegisteredUser(RegistrationPayment $payment): ?User
    {
        $payment = $payment->fresh();

        if ($payment->isPaid()) {
            $this->fulfillIfPaid($payment);
            $payment = $payment->fresh();
        }

        $user = $payment->user;

        if ($user) {
            Auth::login($user);
        }

        return $user;
    }
}
