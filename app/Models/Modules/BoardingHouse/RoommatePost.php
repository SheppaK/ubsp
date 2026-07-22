<?php

namespace App\Models\Modules\BoardingHouse;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoommatePost extends Model
{
    protected $table = 'bh_roommate_posts';

    protected $fillable = [
        'user_id',
        'title',
        'bio',
        'budget',
        'preferred_type',
        'preferred_city',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function typeLabel(): string
    {
        return match ($this->preferred_type) {
            'single' => 'Single Room',
            'double' => 'Double Room',
            'shared' => 'Shared Room',
            'studio' => 'Studio',
            default => 'Any type',
        };
    }
}
