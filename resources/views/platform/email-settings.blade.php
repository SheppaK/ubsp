@extends('layouts.platform')

@section('title', 'Email Settings')
@section('header', 'Email Settings (SMTP)')

@section('content')
<div class="max-w-2xl space-y-6">
    @if(session('success'))
        <div class="bento-card p-4 border-green-300 bg-green-50 font-sans text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bento-card p-4 border-brand-coral/30 bg-brand-coral/5 font-sans text-brand-coral">{{ session('error') }}</div>
    @endif

    <p class="font-sans text-brand-indigo/70">Configure SMTP credentials stored in the database. PHPMailer will use these settings for tenant welcome emails and notifications.</p>

    <form method="POST" action="{{ route('platform.email-settings.update') }}" class="bento-card p-8 space-y-5 hero-animate">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <x-input-label for="host" value="SMTP Host" />
                <x-text-input id="host" name="host" class="block mt-1 w-full" :value="old('host', $settings->host)" required />
                <x-input-error :messages="$errors->get('host')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="port" value="Port" />
                <x-text-input id="port" name="port" type="number" class="block mt-1 w-full" :value="old('port', $settings->port ?? 587)" required />
            </div>
            <div>
                <x-input-label for="encryption" value="Encryption" />
                <select id="encryption" name="encryption" class="block mt-1 w-full rounded-2xl border-brand-lavender/40 font-sans">
                    <option value="tls" @selected(old('encryption', $settings->encryption) === 'tls')>TLS</option>
                    <option value="ssl" @selected(old('encryption', $settings->encryption) === 'ssl')>SSL</option>
                    <option value="" @selected(old('encryption', $settings->encryption) === '')>None</option>
                </select>
            </div>
            <div>
                <x-input-label for="username" value="Username" />
                <x-text-input id="username" name="username" class="block mt-1 w-full" :value="old('username', $settings->username)" />
            </div>
            <div>
                <x-input-label for="password" value="Password" />
                <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" placeholder="{{ $settings->exists ? 'Leave blank to keep current' : '' }}" />
            </div>
            <div>
                <x-input-label for="from_address" value="From Email" />
                <x-text-input id="from_address" name="from_address" type="email" class="block mt-1 w-full" :value="old('from_address', $settings->from_address)" required />
            </div>
            <div>
                <x-input-label for="from_name" value="From Name" />
                <x-text-input id="from_name" name="from_name" class="block mt-1 w-full" :value="old('from_name', $settings->from_name ?? config('app.name'))" />
            </div>
        </div>

        <label class="flex items-center gap-2 font-sans text-sm text-brand-indigo">
            <input type="checkbox" name="is_active" value="1" class="rounded border-brand-lavender text-brand-coral focus:ring-brand-coral" @checked(old('is_active', $settings->is_active))>
            Enable these settings (PHPMailer will use DB credentials)
        </label>

        <button type="submit" class="btn-primary">Save Email Settings</button>
    </form>

    <form method="POST" action="{{ route('platform.email-settings.test') }}" class="bento-card p-6 space-y-4">
        @csrf
        <h3 class="font-heading font-semibold text-brand-indigo">Send Test Email</h3>
        <div class="flex gap-3">
            <x-text-input name="test_email" type="email" class="flex-1" placeholder="test@example.com" required />
            <button type="submit" class="btn-secondary">Send Test</button>
        </div>
    </form>
</div>
@endsection
