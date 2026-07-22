<?php

namespace App\Http\Middleware;

use App\Services\ModuleManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleEnabled
{
    public function __construct(protected ModuleManager $modules) {}

    public function handle(Request $request, Closure $next, string $module): Response
    {
        if (! $this->modules->isEnabled($module)) {
            abort(403, 'This module is currently disabled.');
        }

        $config = $this->modules->get($module);

        if ($config && $request->user() && ! $request->user()->hasRole('super-admin')) {
            if (! $request->user()->can($config['permission'] ?? "{$module}.access")) {
                abort(403, 'You do not have access to this module.');
            }
        }

        return $next($request);
    }
}
