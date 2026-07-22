<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Modules\BoardingHouse\Landlord;
use App\Services\BusinessRegistrationService;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected BusinessRegistrationService $registration,
    ) {}

    public function index(Request $request): View
    {
        $business = $this->resolveBusiness($request);

        $tenants = $business->members()
            ->where('role', 'tenant')
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('modules.boarding-house.admin.tenants.index', [
            'config' => $this->modules->get('boarding-house'),
            'business' => $business,
            'tenants' => $tenants,
        ]);
    }

    public function create(Request $request): View
    {
        $business = $this->resolveBusiness($request);

        return view('modules.boarding-house.admin.tenants.create', [
            'config' => $this->modules->get('boarding-house'),
            'business' => $business,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $business = $this->resolveBusiness($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $this->registration->createTenantAccount($business, $request->user(), $validated);

        return redirect()
            ->route('modules.boarding-house.admin.tenants.index')
            ->with('success', 'Tenant account created. Login credentials have been sent to '.$validated['email'].'.');
    }

    protected function resolveBusiness(Request $request): Business
    {
        $user = $request->user();
        $landlord = Landlord::where('user_id', $user->id)->first();

        if ($landlord?->business_id) {
            return Business::findOrFail($landlord->business_id);
        }

        $business = $user->ownedBusiness ?? $user->businesses()->first();

        abort_unless($business, 403, 'You must register as a business owner to manage tenants.');

        return $business;
    }
}
