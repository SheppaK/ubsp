<?php

namespace App\Http\Controllers;

use App\Services\ModuleManager;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function dashboard(): View
    {
        return view('platform.dashboard', [
            'modules' => $this->modules->accessible(),
        ]);
    }

    public function modules(): View
    {
        $user = auth()->user();
        $allModules = $this->modules->all();
        $records = \App\Models\PlatformModule::query()->get()->keyBy('slug');

        return view('platform.modules', [
            'modules' => $allModules,
            'moduleRecords' => $records,
            'canManage' => $user->hasRole(['super-admin', 'administrator']),
        ]);
    }

    public function toggleModule(Request $request, string $slug)
    {
        abort_unless($request->user()->hasRole(['super-admin', 'administrator']), 403);

        $module = \App\Models\PlatformModule::where('slug', $slug)->firstOrFail();
        $module->update(['is_enabled' => ! $module->is_enabled]);

        return back()->with('success', "{$module->name} ".($module->is_enabled ? 'enabled' : 'disabled').'.');
    }

    public function updateModulePrice(Request $request, string $slug): \Illuminate\Http\RedirectResponse
    {
        abort_unless($request->user()->hasRole(['super-admin', 'administrator']), 403);

        $validated = $request->validate([
            'price_zmw' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $module = \App\Models\PlatformModule::where('slug', $slug)->firstOrFail();
        $module->update(['price_zmw' => $validated['price_zmw']]);

        return back()->with('success', "Price for {$module->name} updated to K ".number_format((float) $module->price_zmw, 2).' ZMW.');
    }
}
