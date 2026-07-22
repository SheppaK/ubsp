<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\BalancedScorecard\Kpi;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BalancedScorecardController extends Controller
{
    protected string $slug = 'balanced-scorecard';

    protected string $resource = 'kpis';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => Kpi::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'kpis' => Kpi::latest()->paginate(15),
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
            'objective_id' => ['required', 'exists:bsc_objectives,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'target' => ['required', 'numeric'],
            'actual' => ['nullable', 'numeric'],
            'status' => ['nullable', 'in:green,yellow,red'],
        ]);

        Kpi::create([...$validated, 'status' => $validated['status'] ?? 'green']);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'KPI created successfully.');
    }

    public function edit(Kpi $kpi): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'kpi' => $kpi,
        ]);
    }

    public function update(Request $request, Kpi $kpi): RedirectResponse
    {
        $validated = $request->validate([
            'objective_id' => ['required', 'exists:bsc_objectives,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:255'],
            'target' => ['required', 'numeric'],
            'actual' => ['nullable', 'numeric'],
            'status' => ['nullable', 'in:green,yellow,red'],
        ]);

        $kpi->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'KPI updated successfully.');
    }

    public function destroy(Kpi $kpi): RedirectResponse
    {
        $kpi->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'KPI deleted successfully.');
    }
}
