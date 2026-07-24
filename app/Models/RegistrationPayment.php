<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class RegistrationPayment extends Model
{
    protected $fillable = [
        'seller_reference',
        'amount_zmw',
        'currency',
        'status',
        'payment_method',
        'network',
        'phone',
        'token',
        'transaction_id',
        'modules',
        'registration_payload',
        'kcpay_init_response',
        'kcpay_callback_payload',
        'business_id',
        'user_id',
        'paid_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_zmw' => 'decimal:2',
            'modules' => 'array',
            'kcpay_init_response' => 'array',
            'kcpay_callback_payload' => 'array',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRegistrationData(): array
    {
        try {
            return json_decode(Crypt::decryptString($this->registration_payload), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return [];
        }
    }

    public static function encryptPayload(array $data): string
    {
        return Crypt::encryptString(json_encode($data, JSON_THROW_ON_ERROR));
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing'], true);
    }

    public static function generateReference(): string
    {
        return 'UBSP-'.now()->format('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }
}
