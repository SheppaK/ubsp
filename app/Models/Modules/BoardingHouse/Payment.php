<?php

namespace App\Models\Modules\BoardingHouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'bh_payments';

    protected $fillable = [
        'booking_request_id',
        'stripe_session_id',
        'stripe_payment_intent',
        'amount',
        'currency',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}
