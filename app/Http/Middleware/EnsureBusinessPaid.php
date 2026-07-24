<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessPaid
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasRole(['super-admin', 'administrator'])) {
            return $next($request);
        }

        $business = $user->ownedBusiness;

        if (! $business) {
            return $next($request);
        }

        if ($business->hasPaid()) {
            return $next($request);
        }

        if ($request->routeIs(
            'platform.business.payment',
            'platform.business.payment.*',
            'register.business.checkout',
            'register.business.pay',
            'register.business.waiting',
            'register.business.status',
            'register.business.success',
            'register.business.return',
            'logout',
            'profile.*',
        )) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Payment required before accessing modules.',
                'redirect' => route('platform.business.payment'),
            ], 402);
        }

        return redirect()
            ->route('platform.business.payment')
            ->with('error', 'Please complete payment to unlock your modules.');
    }
}
