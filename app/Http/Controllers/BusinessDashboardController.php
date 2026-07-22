<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\ModuleManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessDashboardController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

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
