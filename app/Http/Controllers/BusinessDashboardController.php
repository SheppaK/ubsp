<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\KcpayService;
use App\Services\ModuleManager;
use App\Services\RegistrationPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected RegistrationPaymentService $payments,
        protected KcpayService $kcpay,
    ) {}

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $business = $user->ownedBusiness ?? $user->businesses()->first();

        abort_unless($business, 404, 'No business found for your account.');

        $activeSlugs = $business->activeModuleSlugs();
        $businessModules = $this->modules->all()
            ->filter(fn (array $module, string $slug) => in_array($slug, $activeSlugs, true));

        return view('platform.business.dashboard', [
            'business' => $business,
            'modules' => $businessModules,
            'memberCount' => $business->members()->count(),
        ]);
    }

    public function payment(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $business = $user->ownedBusiness;

        abort_unless($business, 404, 'No business found for your account.');

        if ($business->hasPaid()) {
            return redirect()
                ->route('platform.business.dashboard')
                ->with('success', 'Your modules are already unlocked.');
        }

        $payment = $this->payments->pendingPaymentForBusiness($business);

        abort_unless($payment, 404, 'No pending payment found. Please contact support.');

        $pricedModules = $this->kcpay->pricedModules($payment->modules ?? []);

        return view('platform.business.payment', [
            'business' => $business,
            'payment' => $payment,
            'pricedModules' => $pricedModules,
            'kcpayReady' => $this->kcpay->isReady(),
            'simulating' => (bool) $this->kcpay->settings()?->shouldSimulateLocally(),
        ]);
    }

    public function users(Request $request): View
    {
        $business = $this->resolveBusiness($request);

        $members = $business->members()
            ->with(['user', 'inviter'])
            ->latest()
            ->paginate(20);

        return view('platform.business.users', [
            'business' => $business,
            'members' => $members,
        ]);
    }

    protected function resolveBusiness(Request $request): Business
    {
        $user = $request->user();
        $business = $user->ownedBusiness ?? $user->businesses()->first();

        abort_unless($business, 404);
        abort_unless(
            $business->owner_id === $user->id || $user->hasRole(['super-admin', 'administrator']),
            403
        );

        return $business;
    }
}
