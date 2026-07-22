<div class="relative group/card">
<a href="{{ route('modules.boarding-house.search.show', $property) }}" class="bento-card stagger-item overflow-hidden p-0 group block" data-hover-lift>
    <div class="relative h-44 bg-brand-lavender/30">
        @if($property->coverUrl())
            <img src="{{ $property->coverUrl() }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <span class="text-5xl font-heading font-bold text-brand-indigo/20">{{ substr($property->name, 0, 1) }}</span>
            </div>
        @endif
        @if($property->landlord?->is_verified)
            <span class="absolute top-3 left-3 tag-accent text-xs">✓ Verified Landlord</span>
        @endif
        @if($property->availableRoomsCount() > 0)
            <span class="absolute top-3 right-3 tag text-xs">{{ $property->availableRoomsCount() }} rooms free</span>
        @else
            <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs bg-brand-coral/90 text-white">Fully Booked</span>
        @endif
    </div>
    <div class="p-5">
        <h3 class="font-heading font-semibold text-lg text-brand-indigo group-hover:text-brand-coral transition">{{ $property->name }}</h3>
        <p class="font-sans text-sm text-brand-indigo/60 mt-1">{{ $property->city }} · {{ $property->address }}</p>
        <div class="flex flex-wrap gap-1 mt-3">
            @if($property->computedDistanceKm() !== null)
                <span class="tag text-xs">{{ $property->computedDistanceKm() }}km to campus</span>
            @endif
            @if($property->hasVirtualTour())
                <span class="tag text-xs">360° Tour</span>
            @endif
            @foreach(array_slice($property->amenities ?? [], 0, 3) as $amenity)
                <span class="tag text-xs">{{ $amenity }}</span>
            @endforeach
        </div>
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-brand-lavender/30">
            <div>
                <span class="font-heading text-xl font-bold text-brand-indigo">${{ number_format($property->minPrice() ?? 0) }}</span>
                <span class="font-sans text-xs text-brand-indigo/60">/month</span>
            </div>
            @if($property->averageRating())
                <div class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= round($property->averageRating()) ? 'text-brand-amber' : 'text-brand-lavender' }}">★</span>
                    @endfor
                    <span class="font-sans text-xs text-brand-indigo/60 ml-1">({{ $property->reviews->count() }})</span>
                </div>
            @endif
        </div>
    </div>
</a>
@if(isset($isFavorite) || isset($inCompare))
    <div class="absolute bottom-4 right-4 flex gap-1 z-10" onclick="event.preventDefault(); event.stopPropagation();">
        @if(isset($isFavorite))
            <form method="POST" action="{{ $isFavorite ? route('modules.boarding-house.favorites.destroy', $property) : route('modules.boarding-house.favorites.store', $property) }}">
                @csrf @if($isFavorite) @method('DELETE') @endif
                <button type="submit" class="p-2 rounded-full bg-white/90 shadow text-sm {{ $isFavorite ? 'text-brand-coral' : 'text-brand-indigo/50' }}" title="Wishlist">♥</button>
            </form>
        @endif
        @if(isset($inCompare) && !$inCompare)
            <form method="POST" action="{{ route('modules.boarding-house.compare.store', $property) }}">
                @csrf
                <button type="submit" class="p-2 rounded-full bg-white/90 shadow text-xs text-brand-indigo" title="Compare">⇄</button>
            </form>
        @endif
    </div>
@endif
</div>
