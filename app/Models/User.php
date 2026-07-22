<?php

namespace App\Models;

use App\Models\Modules\BoardingHouse\Landlord;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'google_id',
        'two_factor_secret',
        'two_factor_enabled',
        'theme',
        'created_by_business_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function ownedBusiness(): HasOne
    {
        return $this->hasOne(Business::class, 'owner_id');
    }

    public function businesses(): BelongsToMany
    {
        return $this->belongsToMany(Business::class, 'business_users')
            ->withPivot('role', 'invited_by')
            ->withTimestamps();
    }

    public function createdByBusiness(): BelongsTo
    {
        return $this->belongsTo(Business::class, 'created_by_business_id');
    }

    public function landlordProfile(): HasOne
    {
        return $this->hasOne(Landlord::class);
    }

    public function isBusinessOwner(): bool
    {
        return $this->hasRole('business-owner') || $this->ownedBusiness()->exists();
    }
}
