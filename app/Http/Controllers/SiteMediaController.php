<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteMediaController extends Controller
{
    public function edit(): View
    {
        $settings = SiteSetting::query()->where('is_active', true)->latest()->first()
            ?? new SiteSetting(SiteSetting::defaults());

        return view('platform.site-media', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_short_name' => ['required', 'string', 'max:64'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,svg,ico,webp', 'max:512'],
            'remove_logo' => ['boolean'],
            'remove_favicon' => ['boolean'],
        ]);

        $settings = SiteSetting::query()->where('is_active', true)->latest()->first()
            ?? new SiteSetting(SiteSetting::defaults());

        if ($request->boolean('remove_logo') && $settings->logo_path) {
            Storage::disk('public')->delete($settings->logo_path);
            $settings->logo_path = null;
        }

        if ($request->boolean('remove_favicon') && $settings->favicon_path) {
            Storage::disk('public')->delete($settings->favicon_path);
            $settings->favicon_path = null;
        }

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            $settings->logo_path = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $settings->favicon_path = $request->file('favicon')->store('branding', 'public');
        }

        $settings->fill([
            'site_name' => $validated['site_name'],
            'site_short_name' => $validated['site_short_name'],
            'tagline' => $validated['tagline'] ?? null,
            'is_active' => true,
        ]);

        if (! $settings->exists) {
            SiteSetting::query()->update(['is_active' => false]);
        }

        $settings->save();
        SiteSetting::clearCache();

        return back()->with('success', 'Site branding updated successfully.');
    }

    public function reset(): RedirectResponse
    {
        $settings = SiteSetting::query()->where('is_active', true)->latest()->first();

        if ($settings) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }
            if ($settings->favicon_path) {
                Storage::disk('public')->delete($settings->favicon_path);
            }
            $settings->delete();
        }

        SiteSetting::clearCache();

        return back()->with('success', 'Site branding reset to defaults.');
    }
}
