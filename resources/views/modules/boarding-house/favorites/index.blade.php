@extends('layouts.platform')

@section('title', 'My Wishlist')
@section('header', 'Saved Properties')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex items-center justify-between">
        <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
        <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-primary text-sm">Browse More</a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($favorites as $favorite)
            <div class="relative">
                @include('modules.boarding-house.partials.property-card', ['property' => $favorite->property])
                <form method="POST" action="{{ route('modules.boarding-house.favorites.destroy', $favorite->property) }}" class="absolute top-3 right-3 z-10">
                    @csrf @method('DELETE')
                    <button type="submit" class="p-2 rounded-full bg-white/90 text-brand-coral hover:bg-brand-coral hover:text-white transition shadow" title="Remove">♥</button>
                </form>
            </div>
        @empty
            <div class="col-span-full bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo mb-2">No saved properties yet</p>
                <p class="font-sans text-brand-indigo/60 mb-4">Bookmark listings while browsing to build your wishlist.</p>
                <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-primary">Start Browsing</a>
            </div>
        @endforelse
    </div>

    {{ $favorites->links() }}
</div>
@endsection
