<?php

namespace App\Providers;

use App\Services\ModuleManager;
use App\Services\PhpMailerService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class UbspServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class);
        $this->app->singleton(PhpMailerService::class);
    }

    public function boot(): void
    {
        try {
            app(PhpMailerService::class)->applyToLaravelConfig();
        } catch (\Throwable) {
            // Database may not be migrated yet during install.
        }

        View::composer('layouts.platform', function ($view) {
            if (auth()->check()) {
                $view->with('accessibleModules', app(ModuleManager::class)->accessible());
            }
        });
    }
}
