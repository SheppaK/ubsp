<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\SportsLeague\League;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SportsLeagueController extends Controller
{
    protected string $slug = 'sports-league';

    protected string $resource = 'leagues';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => League::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'leagues' => League::latest()->paginate(15),
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
            'season' => ['nullable', 'string', 'max:255'],
        ]);

        League::create($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'League created successfully.');
    }

    public function edit(League $league): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'league' => $league,
        ]);
    }

    public function update(Request $request, League $league): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'season' => ['nullable', 'string', 'max:255'],
        ]);

        $league->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'League updated successfully.');
    }

    public function destroy(League $league): RedirectResponse
    {
        $league->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'League deleted successfully.');
    }
}
