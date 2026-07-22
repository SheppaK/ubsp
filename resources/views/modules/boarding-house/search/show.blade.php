@extends('layouts.platform')

@section('title', $property->name)
@section('header', $property->name)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="space-y-6">
    <a href="{{ route('modules.boarding-house.search.index') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral transition hero-animate">&larr; Back to search</a>

    <div class="flex flex-wrap gap-2 hero-animate">
        <form method="POST" action="{{ $isFavorite ? route('modules.boarding-house.favorites.destroy', $property) : route('modules.boarding-house.favorites.store', $property) }}" class="inline">
            @csrf @if($isFavorite) @method('DELETE') @endif
            <button type="submit" class="btn-secondary text-sm">{{ $isFavorite ? '♥ Saved' : '♡ Save' }}</button>
        </form>
        @unless($inCompare)
            <form method="POST" action="{{ route('modules.boarding-house.compare.store', $property) }}" class="inline">
                @csrf
                <button type="submit" class="btn-secondary text-sm">+ Compare</button>
            </form>
        @endunless
        <a href="{{ route('modules.boarding-house.compare.index') }}" class="btn-ghost text-sm">View Compare</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Gallery --}}
            <div class="bento-card overflow-hidden p-0 hero-animate">
                <div class="h-64 lg:h-80 bg-brand-lavender/30 flex items-center justify-center">
                    @if($property->coverUrl())
                        <img src="{{ $property->coverUrl() }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-6xl font-heading font-bold text-brand-indigo/20">{{ substr($property->name, 0, 1) }}</span>
                    @endif
                </div>
                @if($property->images->count())
                    <div class="flex gap-2 p-3 overflow-x-auto">
                        @foreach($property->images as $image)
                            <img src="{{ $image->url() }}" alt="" class="w-20 h-20 rounded-xl object-cover shrink-0">
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Virtual Tours --}}
            @if($property->hasVirtualTour())
                <div class="bento-card stagger-item p-8">
                    <h3 class="font-heading font-semibold text-brand-indigo mb-4">Virtual Tour</h3>
                    @if($property->virtual_tour_video_url)
                        <div class="aspect-video rounded-2xl overflow-hidden bg-black mb-4">
                            <iframe src="{{ $property->virtual_tour_video_url }}" class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                        </div>
                    @endif
                    @if($property->virtual_tour_360_url)
                        <div class="aspect-video rounded-2xl overflow-hidden bg-brand-lavender/20">
                            <iframe src="{{ $property->virtual_tour_360_url }}" class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                        </div>
                        <p class="font-sans text-xs text-brand-indigo/50 mt-2">360° panorama — drag to look around</p>
                    @endif
                </div>
            @endif

            {{-- Details --}}
            <div class="bento-card stagger-item p-8">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <h1 class="font-heading text-2xl font-bold text-brand-indigo">{{ $property->name }}</h1>
                        <p class="font-sans text-brand-indigo/60 mt-1">{{ $property->address }}, {{ $property->city }}</p>
                    </div>
                    @if($property->averageRating())
                        <div class="bento-card-accent px-4 py-2 text-center">
                            <p class="font-heading text-2xl font-bold">★ {{ number_format($property->averageRating(), 1) }}</p>
                            <p class="text-xs font-sans">{{ $property->reviews->count() }} reviews</p>
                        </div>
                    @endif
                </div>
                <p class="font-sans text-brand-indigo/80 leading-relaxed">{{ $property->description }}</p>
                @if($property->amenities)
                    <div class="flex flex-wrap gap-2 mt-6">
                        @foreach($property->amenities as $amenity)
                            <span class="tag">{{ $amenity }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Map --}}
            @if($property->latitude && $property->longitude)
                <div class="bento-card stagger-item p-0 overflow-hidden">
                    <div class="p-4 border-b border-brand-lavender/30">
                        <h3 class="font-heading font-semibold text-brand-indigo">Location</h3>
                        <p class="font-sans text-sm text-brand-indigo/60">{{ $property->distance_to_campus_km ?? $campusDistance }} km from {{ $campus['name'] ?? 'campus' }}</p>
                    </div>
                    <div id="property-map" class="h-64 w-full" data-lat="{{ $property->latitude }}" data-lng="{{ $property->longitude }}" data-name="{{ $property->name }}"></div>
                </div>
            @endif

            {{-- Reviews --}}
            <div class="bento-card stagger-item p-8">
                <h3 class="font-heading font-semibold text-brand-indigo mb-4">Reviews ({{ $property->reviews->count() }})</h3>
                @forelse($property->reviews as $review)
                    <div class="border-b border-brand-lavender/20 py-4 last:border-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-heading font-semibold text-brand-indigo">{{ $review->user->name }}</span>
                            <span class="text-brand-amber">{{ str_repeat('★', $review->rating) }}</span>
                        </div>
                        <p class="font-sans text-sm text-brand-indigo/70">{{ $review->comment }}</p>
                    </div>
                @empty
                    <p class="font-sans text-brand-indigo/50">No reviews yet. Be the first!</p>
                @endforelse

                @if(auth()->check() && !$userReview)
                    <form method="POST" action="{{ route('modules.boarding-house.reviews.store', $property) }}" class="mt-6 pt-6 border-t border-brand-lavender/30 space-y-4">
                        @csrf
                        <h4 class="font-heading font-medium text-brand-indigo">Leave a Review</h4>
                        <div>
                            <label class="block text-sm font-sans mb-1">Rating</label>
                            <select name="rating" class="input-field w-32" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}">{{ $i }} stars</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-sans mb-1">Comment</label>
                            <textarea name="comment" rows="3" class="input-field" placeholder="Share your experience..."></textarea>
                        </div>
                        <button type="submit" class="btn-secondary text-sm">Submit Review</button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Sidebar: Rooms & Booking --}}
        <div class="space-y-4">
            <div class="bento-card-dark stagger-item p-6 sticky top-24">
                <h3 class="font-heading font-semibold text-brand-cream mb-4">Available Rooms</h3>
                @forelse($property->rooms as $room)
                    <div class="border border-brand-lavender/20 rounded-2xl p-4 mb-3 {{ !$room->is_available ? 'opacity-50' : '' }}">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-heading font-semibold text-brand-cream">{{ $room->name }}</p>
                                <p class="text-xs font-sans text-brand-lavender">{{ $room->typeLabel() }} · {{ $room->capacity }} person(s)</p>
                            </div>
                            <p class="font-heading font-bold text-brand-amber">${{ number_format($room->price) }}/mo</p>
                        </div>
                        @if($room->description)
                            <p class="text-xs font-sans text-brand-lavender mt-2">{{ $room->description }}</p>
                        @endif
                        <a href="{{ route('modules.boarding-house.availability.show', [$property, $room]) }}" class="text-xs font-sans text-brand-amber hover:underline mt-2 inline-block">View calendar</a>

                        @if($room->is_available && $canBook)
                            <form method="POST" action="{{ route('modules.boarding-house.bookings.store', [$property, $room]) }}" class="mt-4 space-y-3 border-t border-brand-lavender/20 pt-4">
                                @csrf
                                <div>
                                    <label class="text-xs font-sans text-brand-lavender">Move-in date</label>
                                    <input type="date" name="move_in_date" class="input-field text-sm py-2" min="{{ date('Y-m-d') }}" required>
                                </div>
                                <div>
                                    <label class="text-xs font-sans text-brand-lavender">Duration (months)</label>
                                    <select name="duration_months" class="input-field text-sm py-2">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ $m }} month{{ $m > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs font-sans text-brand-lavender">Message to landlord</label>
                                    <textarea name="message" rows="2" class="input-field text-sm py-2" placeholder="Introduce yourself..."></textarea>
                                </div>
                                <button type="submit" class="btn-primary w-full text-sm">Request Booking</button>
                            </form>
                        @elseif(!$room->is_available)
                            <p class="text-xs font-sans text-brand-coral mt-2">Currently unavailable</p>
                        @endif
                    </div>
                @empty
                    <p class="font-sans text-brand-lavender text-sm">No rooms listed yet.</p>
                @endforelse
            </div>

            @if($property->landlord)
                <div class="bento-card stagger-item p-5">
                    <p class="text-xs font-sans text-brand-indigo/60 uppercase tracking-wide">Landlord</p>
                    <p class="font-heading font-semibold text-brand-indigo mt-1">{{ $property->landlord->business_name ?? $property->landlord->user->name }}</p>
                    @if($property->landlord->is_verified)
                        <span class="tag-accent text-xs mt-2 inline-block">Verified</span>
                    @endif
                    @if($property->landlord->phone)
                        <p class="font-sans text-sm text-brand-indigo/70 mt-2">{{ $property->landlord->phone }}</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('property-map');
    if (!el) return;
    const map = L.map(el).setView([parseFloat(el.dataset.lat), parseFloat(el.dataset.lng)], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);
    L.marker([parseFloat(el.dataset.lat), parseFloat(el.dataset.lng)]).addTo(map).bindPopup(el.dataset.name);
});
</script>
@endpush
@endsection
