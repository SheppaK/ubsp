<?php

namespace App\Models\Modules\BoardingHouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $table = 'bh_rooms';

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'price',
        'capacity',
        'type',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(RoomAvailability::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'single' => 'Single Room',
            'double' => 'Double Room',
            'shared' => 'Shared Room',
            'studio' => 'Studio',
            default => ucfirst($this->type),
        };
    }
}
