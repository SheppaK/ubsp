<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ThemeSetting extends Model
{
    protected $fillable = [
        'color_lavender',
        'color_indigo',
        'color_indigo_dark',
        'color_amber',
        'color_cream',
        'color_coral',
        'color_page_dark',
        'color_surface_dark',
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
        return config('ubsp.theme_defaults', []);
    }

    public static function current(): self
    {
        return Cache::remember('ubsp.theme_settings', 3600, function () {
            $settings = static::query()->where('is_active', true)->latest()->first();

            if ($settings) {
                return $settings;
            }

            return new static(static::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('ubsp.theme_settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public function toCssVariables(): array
    {
        return [
            '--color-lavender' => $this->hexToRgb($this->color_lavender),
            '--color-indigo' => $this->hexToRgb($this->color_indigo),
            '--color-indigo-dark' => $this->hexToRgb($this->color_indigo_dark),
            '--color-amber' => $this->hexToRgb($this->color_amber),
            '--color-cream' => $this->hexToRgb($this->color_cream),
            '--color-coral' => $this->hexToRgb($this->color_coral),
            '--color-page-dark' => $this->hexToRgb($this->color_page_dark),
            '--color-surface-dark' => $this->hexToRgb($this->color_surface_dark),
        ];
    }

    protected function hexToRgb(string $hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "{$r} {$g} {$b}";
    }
}
