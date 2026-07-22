<?php

namespace App\Models;

use App\Models\Modules\BoardingHouse\Landlord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Business extends Model
{
    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'phone',
        'address',
        'status',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(BusinessModule::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(BusinessUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->withPivot('role', 'invited_by')
            ->withTimestamps();
    }

    public function landlord(): HasMany
    {
        return $this->hasMany(Landlord::class);
    }

    public function activeModuleSlugs(): array
    {
        return $this->modules()->where('is_active', true)->pluck('module_slug')->all();
    }

    public function hasModule(string $slug): bool
    {
        return $this->modules()->where('module_slug', $slug)->where('is_active', true)->exists();
    }

    public static function generateSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
