<?php

namespace App\Services;

use App\Models\PlatformModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ModuleManager
{
    public function all(): Collection
    {
        return collect(config('ubsp.modules', []));
    }

    public function get(string $slug): ?array
    {
        return config("ubsp.modules.{$slug}");
    }

    public function enabled(): Collection
    {
        $enabledSlugs = PlatformModule::query()
            ->where('is_enabled', true)
            ->pluck('slug');

        return $this->all()->filter(fn (array $module, string $slug) => $enabledSlugs->contains($slug));
    }

    public function accessible(): Collection
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        return $this->enabled()->filter(function (array $module, string $slug) use ($user) {
            if ($user->hasRole('super-admin')) {
                return true;
            }

            return $user->can($module['permission'] ?? "{$slug}.access");
        });
    }

    public function isEnabled(string $slug): bool
    {
        return PlatformModule::query()
            ->where('slug', $slug)
            ->where('is_enabled', true)
            ->exists();
    }
}
