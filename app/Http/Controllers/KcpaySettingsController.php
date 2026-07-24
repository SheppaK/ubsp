<?php

namespace App\Http\Controllers;

use App\Models\KcpaySetting;
use App\Services\KcpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KcpaySettingsController extends Controller
{
    public function __construct(protected KcpayService $kcpay) {}

    public function edit(): View
    {
        $settings = KcpaySetting::query()->latest()->first() ?? new KcpaySetting([
            'base_url' => 'https://productcheckout.kundananjicreations.com/',
            'source_name' => config('ubsp.short_name', 'UBSP'),
            'mode' => 'test',
        ]);

        return view('platform.kcpay-settings', [
            'settings' => $settings,
            'callbackUrl' => $this->kcpay->callbackUrl(),
            'initiateEndpoint' => $this->kcpay->initiateEndpoint($settings->base_url),
            'requestStatusEndpoint' => $this->kcpay->requestStatusEndpoint($settings->base_url),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $existing = KcpaySetting::query()->latest()->first();

        $validated = $request->validate([
            'base_url' => ['required', 'url', 'max:255'],
            'api_username' => ['required', 'string', 'max:255'],
            'api_password' => [$existing ? 'nullable' : 'required', 'string', 'max:255'],
            'public_key' => ['nullable', 'string', 'max:500'],
            'private_key' => ['nullable', 'string', 'max:2000'],
            'product_reference' => ['required', 'string', 'max:255'],
            'mode' => ['required', 'in:test,production'],
            'callback_url' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
            'simulate_locally' => ['boolean'],
        ]);

        $settings = $existing ?? new KcpaySetting;

        if (empty($validated['api_password']) && $settings->exists) {
            unset($validated['api_password']);
        }

        if (empty($validated['private_key'])) {
            unset($validated['private_key']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $validated['simulate_locally'] = $request->boolean('simulate_locally');
        $validated['base_url'] = rtrim($validated['base_url'], '/').'/';

        // Never allow simulation in production mode.
        if ($validated['mode'] === 'production') {
            $validated['simulate_locally'] = false;
        }

        if ($validated['is_active']) {
            KcpaySetting::query()->update(['is_active' => false]);
        }

        $settings->fill($validated)->save();
        KcpaySetting::clearCache();

        return back()->with('success', 'KC Pay settings saved successfully.');
    }

    public function testConnection(): RedirectResponse
    {
        $result = $this->kcpay->testConnection();

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message'],
        );
    }
}
