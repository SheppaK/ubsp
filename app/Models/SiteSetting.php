<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_short_name',
        'tagline',
        'logo_path',
        'favicon_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function defaults(): array
    {
        return [
            'site_name' => config('ubsp.name', 'Universal Business Systems Platform'),
            'site_short_name' => config('ubsp.short_name', 'UBSP'),
            'tagline' => null,
            'logo_path' => null,
            'favicon_path' => null,
            'is_active' => true,
        ];
    }

    public static function current(): self
    {
        try {
            return Cache::remember('ubsp.site_settings', 3600, function () {
                $settings = static::query()->where('is_active', true)->latest()->first();

                if ($settings) {
                    return $settings;
                }

                return new static(static::defaults());
            });
        } catch (\Throwable) {
            return new static(static::defaults());
        }
    }

    public static function clearCache(): void
    {
        Cache::forget('ubsp.site_settings');
    }

    public function logoUrl(): ?string
    {
        if (! $this->logo_path) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function faviconUrl(): ?string
    {
        if (! $this->favicon_path) {
            return null;
        }

        return Storage::disk('public')->url($this->favicon_path);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}
