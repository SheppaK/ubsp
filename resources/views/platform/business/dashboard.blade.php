@extends('layouts.platform')

@section('title', 'My Business')
@section('header', $business->name)

@section('content')
<div class="space-y-8">
    @if(session('success'))
        <div class="bento-card p-4 border-brand-coral/30 bg-brand-coral/5 font-sans text-brand-indigo">{{ session('success') }}</div>
    @endif

    <div class="bento-card-dark hero-animate p-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h2 class="font-heading text-2xl font-bold text-brand-cream">{{ $business->name }}</h2>
                <p class="font-sans text-brand-lavender mt-2">Manage your modules, team members, and business settings.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('platform.business.users') }}" class="btn-secondary">Team Members ({{ $memberCount }})</a>
            </div>
        </div>
    </div>

    <div>
        <h3 class="font-heading text-xl font-semibold mb-4 text-brand-indigo dark:text-brand-cream">Your Modules</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($modules as $slug => $mod)
                <a href="{{ route('modules.'.$slug.'.dashboard') }}" class="bento-card stagger-item block hover:border-brand-coral/40 transition" data-hover-lift>
                    <div class="w-10 h-10 rounded-xl {{ $mod['color'] }} mb-3"></div>
                    <h4 class="font-heading font-semibold text-brand-indigo dark:text-brand-cream">{{ $mod['name'] }}</h4>
                    <p class="font-sans text-sm text-brand-indigo/60 dark:text-brand-lavender mt-1">{{ $mod['description'] }}</p>
                    @if($slug === 'boarding-house')
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="text-xs font-sans px-2 py-1 rounded-lg bg-brand-lavender/20 text-brand-indigo">List Properties</span>
                            <span class="text-xs font-sans px-2 py-1 rounded-lg bg-brand-lavender/20 text-brand-indigo">Add Tenants</span>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    @if($modules->has('boarding-house'))
        <div class="bento-card p-6">
            <h3 class="font-heading text-lg font-semibold text-brand-indigo dark:text-brand-cream mb-4">Boarding House Quick Actions</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('modules.boarding-house.admin.properties.create') }}" class="btn-primary">Add Property</a>
                <a href="{{ route('modules.boarding-house.admin.properties.index') }}" class="btn-secondary">Manage Properties</a>
                <a href="{{ route('modules.boarding-house.admin.tenants.index') }}" class="btn-secondary">Manage Tenants</a>
                <a href="{{ route('modules.boarding-house.admin.bookings.manage') }}" class="btn-ghost">Bookings</a>
                <a href="{{ route('modules.boarding-house.admin.analytics') }}" class="btn-ghost">Analytics</a>
            </div>
        </div>
    @endif
</div>
@endsection
