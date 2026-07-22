@extends('layouts.platform')

@section('title', 'Dashboard')
@section('header', 'Platform Dashboard')

@section('content')
<div class="space-y-8">
    <div class="bento-card-dark hero-animate p-8">
        <h2 class="text-2xl lg:text-3xl font-heading font-bold text-brand-cream">
            Welcome back, {{ auth()->user()->name }}
        </h2>
        <p class="mt-2 font-sans text-brand-lavender">
            Universal Business Systems Platform — access all your business systems from one place.
        </p>
    </div>

    <div>
        <h3 class="font-heading text-xl font-semibold mb-6 text-heading stagger-item">Your Applications</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($modules as $slug => $mod)
                <a href="{{ route('modules.'.$slug.'.dashboard') }}" class="bento-card stagger-item group block" data-hover-lift>
                    <div class="w-12 h-12 rounded-2xl {{ $mod['color'] }} flex items-center justify-center mb-4">
                        <span class="font-heading font-bold text-sm text-white">{{ substr($mod['name'], 0, 1) }}</span>
                    </div>
                    <h4 class="font-heading font-semibold text-heading">{{ $mod['name'] }}</h4>
                    <p class="text-sm font-sans text-muted mt-1 line-clamp-2">{{ $mod['description'] }}</p>
                    <span class="inline-flex items-center mt-4 text-sm font-sans font-medium text-brand-coral group-hover:gap-2 transition-all">
                        Open app
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                </a>
            @empty
                <div class="col-span-full bento-card text-center py-12">
                    <p class="font-sans text-brand-indigo/60">No modules are available. Contact your administrator.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
