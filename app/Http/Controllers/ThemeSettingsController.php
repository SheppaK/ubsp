<?php

namespace App\Http\Controllers;

use App\Models\ThemeSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = ThemeSetting::query()->where('is_active', true)->latest()->first()
            ?? new ThemeSetting(ThemeSetting::defaults());

        return view('platform.theme-settings', [
            'settings' => $settings,
            'presets' => config('ubsp.theme_presets', []),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'color_lavender' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_indigo' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_indigo_dark' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_amber' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_cream' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_coral' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_page_dark' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'color_surface_dark' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        ThemeSetting::query()->update(['is_active' => false]);

        ThemeSetting::create(array_merge($validated, ['is_active' => true]));
        ThemeSetting::clearCache();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return back()->with('success', 'Theme colors updated. Changes apply immediately across the platform.');
    }

    public function reset(): RedirectResponse
    {
        ThemeSetting::query()->update(['is_active' => false]);
        ThemeSetting::create(array_merge(ThemeSetting::defaults(), ['is_active' => true]));
        ThemeSetting::clearCache();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return back()->with('success', 'Theme restored to default brand colors.');
    }
}
