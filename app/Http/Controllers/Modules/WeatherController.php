<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\Weather\Location;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeatherController extends Controller
{
    protected string $slug = 'weather';

    protected string $resource = 'locations';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => Location::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'locations' => Location::latest()->paginate(15),
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
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'forecast_cache' => ['nullable', 'array'],
            'cached_at' => ['nullable', 'date'],
        ]);

        Location::create($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Location created successfully.');
    }

    public function edit(Location $location): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'location' => $location,
        ]);
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'forecast_cache' => ['nullable', 'array'],
            'cached_at' => ['nullable', 'date'],
        ]);

        $location->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Location deleted successfully.');
    }
}
