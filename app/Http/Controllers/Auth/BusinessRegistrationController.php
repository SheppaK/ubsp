<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PlatformModule;
use App\Services\BusinessRegistrationService;
use App\Services\ModuleManager;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class BusinessRegistrationController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected BusinessRegistrationService $registration,
    ) {}

    public function create(): View
    {
        if (request()->hasSession()) {
            request()->session()->regenerateToken();
        }

        $availableModules = $this->modules->enabled()->filter(function (array $module, string $slug) {
            return PlatformModule::where('slug', $slug)->where('is_enabled', true)->exists();
        });

        return view('auth.register-business', [
            'modules' => $availableModules,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $enabledSlugs = PlatformModule::query()->where('is_enabled', true)->pluck('slug')->all();

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'modules' => ['required', 'array', 'min:1'],
            'modules.*' => ['string', 'in:'.implode(',', $enabledSlugs)],
        ]);

        $business = $this->registration->register($validated, $validated['modules']);

        $user = $business->owner;
        event(new Registered($user));
        Auth::login($user);

        return redirect()
            ->route('platform.business.dashboard')
            ->with('success', 'Welcome! Your business "'.$business->name.'" is ready. You can now manage your selected modules.');
    }
}
