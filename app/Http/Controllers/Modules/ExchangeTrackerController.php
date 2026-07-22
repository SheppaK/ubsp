<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\ExchangeTracker\ExchangeRate;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExchangeTrackerController extends Controller
{
    protected string $slug = 'exchange-tracker';

    protected string $resource = 'rates';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => ExchangeRate::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'rates' => ExchangeRate::latest()->paginate(15),
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
            'currency_code' => ['required', 'string', 'size:3'],
            'rate' => ['required', 'numeric'],
            'recorded_date' => ['required', 'date'],
        ]);

        ExchangeRate::create($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Exchange rate created successfully.');
    }

    public function edit(ExchangeRate $rate): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'rate' => $rate,
        ]);
    }

    public function update(Request $request, ExchangeRate $rate): RedirectResponse
    {
        $validated = $request->validate([
            'currency_code' => ['required', 'string', 'size:3'],
            'rate' => ['required', 'numeric'],
            'recorded_date' => ['required', 'date'],
        ]);

        $rate->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Exchange rate updated successfully.');
    }

    public function destroy(ExchangeRate $rate): RedirectResponse
    {
        $rate->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Exchange rate deleted successfully.');
    }
}
