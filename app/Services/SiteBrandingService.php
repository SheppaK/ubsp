<?php

namespace App\Services;

use App\Models\SiteSetting;

class SiteBrandingService
{
    public function current(): SiteSetting
    {
        return SiteSetting::current();
    }

    public function name(): string
    {
        return $this->current()->site_name;
    }

    public function shortName(): string
    {
        return $this->current()->site_short_name;
    }

    public function tagline(): ?string
    {
        return $this->current()->tagline;
    }

    public function hasLogo(): bool
    {
        return filled($this->current()->logo_path);
    }

    public function hasFavicon(): bool
    {
        return filled($this->current()->favicon_path);
    }

    public function logoUrl(): ?string
    {
        return $this->current()->logoUrl();
    }

    public function faviconUrl(): ?string
    {
        return $this->current()->faviconUrl();
    }

    public function initial(): string
    {
        return strtoupper(substr($this->shortName(), 0, 1));
    }
}
