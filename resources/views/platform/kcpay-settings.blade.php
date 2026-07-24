@extends('layouts.platform')

@section('title', 'KC Pay Settings')
@section('header', 'KC Pay Payment Gateway')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
        <div class="bento-card p-4 border-green-300 bg-green-50 font-sans text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bento-card p-4 border-brand-coral/30 bg-brand-coral/5 font-sans text-brand-coral">{{ session('error') }}</div>
    @endif

    <p class="font-sans text-brand-indigo/70">Configure KC Pay credentials for business registration payments. All sensitive values are encrypted in the database.</p>

    <div class="bento-card p-5 font-sans text-sm text-brand-indigo/80 space-y-4">
        <div>
            <p class="font-heading font-semibold text-brand-indigo mb-1">API endpoints</p>
            <p class="text-xs text-brand-indigo/50 mb-1">Initiate payment (POST)</p>
            <p class="break-all text-brand-indigo/70 mb-3">{{ $initiateEndpoint }}</p>
            <p class="text-xs text-brand-indigo/50 mb-1">Check status (POST)</p>
            <p class="break-all text-brand-indigo/70 mb-3">{{ $requestStatusEndpoint }}</p>
            <p class="text-xs text-brand-indigo/50 mb-1">Webhook callback URL</p>
            <p class="break-all text-brand-indigo/70">{{ $callbackUrl }}</p>
            <p class="mt-2 text-xs text-brand-indigo/50">Register the webhook URL in your KC Pay dashboard so payment confirmations are sent automatically.</p>
        </div>
        <form method="POST" action="{{ route('platform.kcpay-settings.test') }}">
            @csrf
            <button type="submit" class="btn-secondary">Test API Connection</button>
        </form>
        <p class="text-xs text-brand-indigo/50">The API must be reachable from this server at <code class="text-brand-indigo">productcheckout.kundananjicreations.com</code>. If the test times out, try mobile data, disable firewall/VPN, or contact Kundananji Creations.</p>
    </div>

    <form method="POST" action="{{ route('platform.kcpay-settings.update') }}" class="bento-card p-8 space-y-5 hero-animate">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-label for="base_url" value="API Base URL" />
                <x-text-input id="base_url" name="base_url" class="block mt-1 w-full" :value="old('base_url', $settings->base_url)" required />
                <x-input-error :messages="$errors->get('base_url')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="api_username" value="API Username" />
                <x-text-input id="api_username" name="api_username" class="block mt-1 w-full" :value="old('api_username', $settings->api_username)" required />
            </div>
            <div>
                <x-input-label for="api_password" value="API Password" />
                <x-text-input id="api_password" name="api_password" type="password" class="block mt-1 w-full" placeholder="{{ $settings->exists ? 'Leave blank to keep current' : '' }}" />
            </div>
            <div>
                <x-input-label for="public_key" value="Public Key (card payments)" />
                <x-text-input id="public_key" name="public_key" class="block mt-1 w-full" :value="old('public_key', $settings->public_key)" />
            </div>
            <div>
                <x-input-label for="private_key" value="Private Key (card payments)" />
                <x-text-input id="private_key" name="private_key" type="password" class="block mt-1 w-full" placeholder="{{ $settings->private_key ? 'Leave blank to keep current' : '' }}" />
            </div>
            <div>
                <x-input-label for="product_reference" value="Product Reference" />
                <x-text-input id="product_reference" name="product_reference" class="block mt-1 w-full" :value="old('product_reference', $settings->product_reference)" required />
            </div>
            <div>
                <x-input-label for="mode" value="Mode" />
                <select id="mode" name="mode" class="block mt-1 w-full rounded-2xl border-brand-lavender/40 font-sans">
                    <option value="test" @selected(old('mode', $settings->mode) === 'test')>Test</option>
                    <option value="production" @selected(old('mode', $settings->mode) === 'production')>Production</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="callback_url" value="Custom Callback URL (optional)" />
                <x-text-input id="callback_url" name="callback_url" class="block mt-1 w-full" :value="old('callback_url', $settings->callback_url)" placeholder="Leave blank to use the default webhook URL above" />
            </div>
        </div>

        <label class="flex items-center gap-2 font-sans text-sm text-brand-indigo">
            <input type="checkbox" name="is_active" value="1" class="rounded border-brand-lavender text-brand-coral focus:ring-brand-coral" @checked(old('is_active', $settings->is_active))>
            Enable KC Pay for business registration payments
        </label>

        <label class="flex items-start gap-2 font-sans text-sm text-brand-indigo">
            <input type="checkbox" name="simulate_locally" value="1" class="mt-0.5 rounded border-brand-lavender text-brand-coral focus:ring-brand-coral" @checked(old('simulate_locally', $settings->simulate_locally))>
            <span>
                <span class="font-medium">Simulate payments locally (Test mode only)</span>
                <span class="block text-xs text-brand-indigo/60 mt-0.5">Use this when your network cannot reach the KC Pay API. Completes registration without calling the live gateway. Disabled automatically in Production mode.</span>
            </span>
        </label>

        <button type="submit" class="btn-primary">Save KC Pay Settings</button>
    </form>
</div>
@endsection
