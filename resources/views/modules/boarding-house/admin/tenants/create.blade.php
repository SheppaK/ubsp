@extends('layouts.platform')

@section('title', 'Add Tenant')
@section('header', 'Create Tenant Account')

@section('content')
<div class="max-w-xl">
    <a href="{{ route('modules.boarding-house.admin.tenants.index') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Back to Tenants</a>

    <form method="POST" action="{{ route('modules.boarding-house.admin.tenants.store') }}" class="bento-card mt-4 p-8 space-y-5 hero-animate">
        @csrf

        <p class="font-sans text-sm text-brand-indigo/60">A temporary password will be generated and sent to the tenant's email. They can log in and use "Forgot Password" to set their own password.</p>

        <div>
            <x-input-label for="name" value="Full Name" />
            <x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name')" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email Address" />
            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="phone" value="Phone (optional)" />
            <x-text-input id="phone" name="phone" class="block mt-1 w-full" :value="old('phone')" />
        </div>

        <button type="submit" class="btn-primary">Create & Email Credentials</button>
    </form>
</div>
@endsection
