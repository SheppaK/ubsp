@extends('layouts.platform')

@section('title', 'Manage Properties')
@section('header', 'Property Listings')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4 hero-animate">
        <div>
            <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
            <h2 class="font-heading text-xl font-bold text-brand-indigo mt-1">Your Properties</h2>
        </div>
        <a href="{{ route('modules.boarding-house.admin.properties.create') }}" class="btn-primary">+ Add Property</a>
    </div>

    <div class="grid gap-4">
        @forelse($properties as $property)
            <div class="bento-card stagger-item flex flex-col sm:flex-row sm:items-center gap-4 p-5" data-hover-lift>
                <div class="w-full sm:w-24 h-24 rounded-2xl bg-brand-lavender/30 shrink-0 overflow-hidden">
                    @if($property->coverUrl())
                        <img src="{{ $property->coverUrl() }}" class="w-full h-full object-cover" alt="">
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-heading font-semibold text-brand-indigo">{{ $property->name }}</h3>
                        <span class="tag text-xs capitalize">{{ $property->status }}</span>
                    </div>
                    <p class="font-sans text-sm text-brand-indigo/60">{{ $property->city }} · {{ $property->rooms->count() }} rooms · {{ $property->rooms->where('is_available', true)->count() }} available</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('modules.boarding-house.admin.properties.show', $property) }}" class="btn-secondary text-sm py-2 px-4">Manage</a>
                    <a href="{{ route('modules.boarding-house.search.show', $property) }}" class="btn-ghost text-sm py-2 px-4">Preview</a>
                </div>
            </div>
        @empty
            <div class="bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo mb-2">No properties yet</p>
                <p class="font-sans text-brand-indigo/60 mb-4">Start by listing your first boarding house.</p>
                <a href="{{ route('modules.boarding-house.admin.properties.create') }}" class="btn-primary">Add Property</a>
            </div>
        @endforelse
    </div>

    {{ $properties->links() }}
</div>
@endsection
