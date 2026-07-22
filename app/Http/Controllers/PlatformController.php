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
        $enabledSlugs = \App\Models\PlatformModule::pluck('is_enabled', 'slug');

        return view('platform.modules', [
            'modules' => $allModules,
            'enabledSlugs' => $enabledSlugs,
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
}
