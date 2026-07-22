<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Modules\ElectronicsTracker\Asset;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElectronicsTrackerController extends Controller
{
    protected string $slug = 'electronics-tracker';

    protected string $resource = 'assets';

    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view("modules.{$this->slug}.dashboard", [
            'config' => $this->modules->get($this->slug),
            'stats' => ['count' => Asset::count()],
        ]);
    }

    public function index(): View
    {
        return view("modules.{$this->slug}.index", [
            'config' => $this->modules->get($this->slug),
            'assets' => Asset::latest()->paginate(15),
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
            'type' => ['required', 'in:computer,phone,laptop,printer,accessory'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:et_assets,serial_number'],
            'qr_code' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expires' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:active,maintenance,disposed'],
            'notes' => ['nullable', 'string'],
        ]);

        Asset::create($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Asset created successfully.');
    }

    public function edit(Asset $asset): View
    {
        return view("modules.{$this->slug}.edit", [
            'config' => $this->modules->get($this->slug),
            'asset' => $asset,
        ]);
    }

    public function update(Request $request, Asset $asset): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:computer,phone,laptop,printer,accessory'],
            'serial_number' => ['required', 'string', 'max:255', 'unique:et_assets,serial_number,'.$asset->id],
            'qr_code' => ['nullable', 'string', 'max:255'],
            'purchase_date' => ['nullable', 'date'],
            'warranty_expires' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:active,maintenance,disposed'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset->update($validated);

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        $asset->delete();

        return redirect()->route("modules.{$this->slug}.{$this->resource}.index")
            ->with('success', 'Asset deleted successfully.');
    }
}
