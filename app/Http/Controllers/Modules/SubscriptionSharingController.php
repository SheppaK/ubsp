<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\SubscriptionSharing\Plan;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionSharingController extends Controller
{
    protected string $slug = 'subscription-sharing';

    protected string $resource = 'plans';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => Plan::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'plans' => Plan::latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view("modules.{$this->slug}.create", [
            'config' => $this->modules->get($this->slug),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'monthly_cost' => ['required', 'numeric', 'min:0'],
            'max_members' => ['required', 'integer', 'min:1'],
            'renewal_date' => ['nullable', 'date'],
        ]);

        Plan::create([...$validated, 'owner_id' => $request->user()->id]);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'plan' => $plan,
        ]);
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'monthly_cost' => ['required', 'numeric', 'min:0'],
            'max_members' => ['required', 'integer', 'min:1'],
            'renewal_date' => ['nullable', 'date'],
            'owner_id' => ['required', 'exists:users,id'],
        ]);

        $plan->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Plan deleted successfully.');
    }
}
