<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicProvider
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if ($user->hasAnyRole(['super-admin', 'administrator', 'doctor', 'manager', 'staff'])) {
            return $next($request);
        }

        if ($user->can('clinic.manage-patients') || $user->can('clinic.manage-appointments')) {
            return $next($request);
        }

        abort(403, 'Healthcare provider access required.');
    }
}
