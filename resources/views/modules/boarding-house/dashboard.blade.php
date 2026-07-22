@extends('layouts.platform')

@section('title', $config['name'])
@section('header', $config['name'])

@section('content')
<div class="space-y-8">
    {{-- Hero actions --}}
    <div class="bento-card-dark hero-animate p-8 lg:p-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h2 class="font-heading text-2xl lg:text-3xl font-bold text-brand-cream">Find Your Perfect Boarding House</h2>
                <p class="font-sans text-brand-lavender mt-2 max-w-xl">Search nearby properties, compare rooms, read reviews, and send booking requests — all in one place.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-primary">Browse Properties</a>
                <a href="{{ route('modules.boarding-house.bookings.mine') }}" class="btn-accent">My Bookings</a>
                <a href="{{ route('modules.boarding-house.favorites.index') }}" class="btn-secondary">Wishlist</a>
                <a href="{{ route('modules.boarding-house.roommates.index') }}" class="btn-secondary">Roommates</a>
                <a href="{{ route('modules.boarding-house.messages.index') }}" class="btn-secondary">Messages</a>
                @if($isManager)
                    <a href="{{ route('modules.boarding-house.admin.properties.index') }}" class="btn-ghost !text-brand-cream !border-white/40">Manage Listings</a>
                    <a href="{{ route('modules.boarding-house.admin.tenants.index') }}" class="btn-ghost !text-brand-cream !border-white/40">Tenants</a>
                    <a href="{{ route('modules.boarding-house.admin.analytics') }}" class="btn-ghost !text-brand-cream !border-white/40">Analytics</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card stagger-item">
            <p class="stat-label">Listed Properties</p>
            <p class="stat-value" data-count="{{ $stats['properties'] }}">{{ $stats['properties'] }}</p>
        </div>
        <div class="stat-card stagger-item">
            <p class="stat-label">Available Rooms</p>
            <p class="stat-value text-brand-coral" data-count="{{ $stats['available_rooms'] }}">{{ $stats['available_rooms'] }}</p>
        </div>
        <div class="stat-card stagger-item">
            <p class="stat-label">My Bookings</p>
            <p class="stat-value">{{ $stats['my_bookings'] }}</p>
        </div>
        @if($isManager)
            <div class="stat-card stagger-item bento-card-accent">
                <p class="stat-label !text-brand-indigo/80">Pending Requests</p>
                <p class="stat-value !text-brand-indigo">{{ $stats['pending_bookings'] }}</p>
                @if($stats['pending_bookings'] > 0)
                    <a href="{{ route('modules.boarding-house.admin.bookings.manage', ['status' => 'pending']) }}" class="text-sm font-sans text-brand-indigo underline mt-1">Review now</a>
                @endif
            </div>
        @endif
    </div>

    {{-- Featured properties --}}
    <div>
        <div class="flex items-center justify-between mb-6 stagger-item">
            <h3 class="font-heading text-xl font-semibold text-heading">Featured Near Campus</h3>
            <a href="{{ route('modules.boarding-house.search.index') }}" class="font-sans text-sm text-brand-coral hover:underline">View all &rarr;</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($recentProperties as $property)
                <a href="{{ route('modules.boarding-house.search.show', $property) }}" class="bento-card stagger-item overflow-hidden p-0 group" data-hover-lift>
                    <div class="h-36 bg-brand-lavender/30 dark:bg-white/10 flex items-center justify-center">
                        @if($property->coverUrl())
                            <img src="{{ $property->coverUrl() }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-4xl font-heading font-bold text-subtle">{{ substr($property->name, 0, 1) }}</span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h4 class="font-heading font-semibold text-heading group-hover:text-brand-coral transition">{{ $property->name }}</h4>
                        <p class="text-xs font-sans text-muted mt-1">{{ $property->city }} · {{ $property->distance_to_campus_km }}km from campus</p>
                        <div class="flex items-center justify-between mt-3">
                            <span class="font-heading font-bold text-heading">${{ number_format($property->minPrice() ?? 0) }}/mo</span>
                            @if($property->averageRating())
                                <span class="tag-accent">★ {{ number_format($property->averageRating(), 1) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    @if($isManager)
        <div class="bento-card stagger-item p-8 border-2 border-dashed border-brand-lavender dark:border-white/20">
            <h3 class="font-heading font-semibold text-heading mb-2">Landlord / Admin Tools</h3>
            <p class="font-sans text-sm text-muted mb-4">List properties, add rooms, upload photos, and manage booking requests.</p>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('modules.boarding-house.admin.properties.create') }}" class="btn-primary">Add New Property</a>
                <a href="{{ route('modules.boarding-house.admin.bookings.manage') }}" class="btn-secondary">Booking Inbox</a>
                <a href="{{ route('modules.boarding-house.admin.analytics') }}" class="btn-secondary">Analytics</a>
            </div>
        </div>
    @else
        <div class="bento-card-accent stagger-item p-8 text-center">
            <h3 class="font-heading font-semibold text-lg">New here?</h3>
            <p class="font-sans text-sm mt-2 opacity-80">Create a free account, browse listings, and send booking requests to landlords instantly.</p>
        </div>
    @endif
</div>
@endsection
