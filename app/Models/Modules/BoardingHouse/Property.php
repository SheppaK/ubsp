<?php

namespace App\Models\Modules\BoardingHouse;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $table = 'bh_properties';

    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'city',
        'latitude',
        'longitude',
        'description',
        'cover_image',
        'amenities',
        'status',
        'distance_to_campus_km',
        'virtual_tour_video_url',
        'virtual_tour_360_url',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'amenities' => 'array',
        ];
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function isFavoritedBy(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function computedDistanceKm(): ?float
    {
        if ($this->distance_to_campus_km !== null) {
            return (float) $this->distance_to_campus_km;
        }

        return app(\App\Services\Modules\BoardingHouse\CampusProximityService::class)
            ->distanceKm(
                $this->latitude !== null ? (float) $this->latitude : null,
                $this->longitude !== null ? (float) $this->longitude : null
            );
    }

    public function hasVirtualTour(): bool
    {
        return filled($this->virtual_tour_video_url) || filled($this->virtual_tour_360_url);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('address', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function minPrice(): ?float
    {
        return $this->rooms()->where('is_available', true)->min('price');
    }

    public function availableRoomsCount(): int
    {
        return $this->rooms()->where('is_available', true)->count();
    }

    public function averageRating(): ?float
    {
        return $this->reviews()->avg('rating');
    }

    public function coverUrl(): string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        $first = $this->images()->first();

        return $first ? asset('storage/'.$first->path) : '';
    }
}
