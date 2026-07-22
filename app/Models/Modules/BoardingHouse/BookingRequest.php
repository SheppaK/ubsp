<?php

namespace App\Models\Modules\BoardingHouse;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingRequest extends Model
{
    protected $table = 'bh_booking_requests';

    protected $fillable = [
        'room_id',
        'user_id',
        'status',
        'move_in_date',
        'message',
        'duration_months',
        'responded_at',
        'responded_by',
        'lease_pdf_path',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'move_in_date' => 'date',
            'responded_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
