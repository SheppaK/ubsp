<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class KcpaySetting extends Model
{
    protected $fillable = [
        'base_url',
        'api_username',
        'api_password',
        'public_key',
        'private_key',
        'product_reference',
        'source_name',
        'mode',
        'callback_url',
        'is_active',
        'simulate_locally',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'simulate_locally' => 'boolean',
        ];
    }

    public function setApiPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['api_password'] = Crypt::encryptString($value);
    }

    public function setPrivateKeyAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $this->attributes['private_key'] = Crypt::encryptString($value);
    }

    public function getDecryptedApiPassword(): ?string
    {
        if (empty($this->attributes['api_password'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['api_password']);
        } catch (\Throwable) {
            return null;
        }
    }

    public function getDecryptedPrivateKey(): ?string
    {
        if (empty($this->attributes['private_key'])) {
            return null;
        }

        try {
            return Crypt::decryptString($this->attributes['private_key']);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function current(): ?self
    {
        return Cache::remember('ubsp.kcpay_settings', 3600, function () {
            return static::query()->where('is_active', true)->latest()->first();
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('ubsp.kcpay_settings');
    }

    public function isConfigured(): bool
    {
        return $this->is_active
            && $this->api_username
            && $this->getDecryptedApiPassword()
            && $this->product_reference
            && $this->base_url;
    }

    public function apiMode(): string
    {
        return $this->mode === 'production' ? 'production' : 'test';
    }

    /**
     * Local simulation is only allowed in test mode.
     */
    public function shouldSimulateLocally(): bool
    {
        return $this->apiMode() === 'test' && (bool) $this->simulate_locally;
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}
