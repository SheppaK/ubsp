<?php

namespace App\Models\Modules\BoardingHouse;

use App\Models\Business;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Landlord extends Model
{
    protected $table = 'bh_landlords';

    protected $fillable = [
        'user_id',
        'business_id',
        'phone',
        'business_name',
        'bio',
        'is_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public static function forUser(User $user): self
    {
        $business = $user->ownedBusiness ?? $user->businesses()->first();

        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'business_id' => $business?->id,
                'phone' => $user->phone ?? $business?->phone,
                'business_name' => $business?->name ?? $user->name.' Properties',
            ]
        );
    }
}
