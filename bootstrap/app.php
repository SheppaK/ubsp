<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/modules.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Hostinger / reverse proxy: correct HTTPS URLs and secure cookies
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'api/kcpay/callback',
        ]);

        $middleware->alias([
            'module.enabled' => \App\Http\Middleware\EnsureModuleEnabled::class,
            'boarding-house.manager' => \App\Http\Middleware\EnsureBoardingHouseManager::class,
            'clinic.provider' => \App\Http\Middleware\EnsureClinicProvider::class,
            'business.paid' => \App\Http\Middleware\EnsureBusinessPaid::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your session expired. Please refresh the page and try again.',
                ], 419);
            }

            $message = 'Your session expired. Please try again.';

            if ($request->is('login', 'register', 'register/business', 'forgot-password', 'reset-password*')) {
                return redirect()
                    ->back()
                    ->withInput($request->except('_token', 'password', 'password_confirmation'))
                    ->with('status', $message);
            }

            return redirect()
                ->route('login')
                ->with('status', $message);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            $status = $e->getStatusCode();

            if (in_array($status, [401, 403, 404, 419, 429, 500, 503], true)
                && view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", [], $status);
            }

            return null;
        });
    })->create();
