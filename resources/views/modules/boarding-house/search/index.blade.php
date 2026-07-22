@extends('layouts.platform')

@section('title', 'Browse Boarding Houses')
@section('header', 'Find Boarding Houses')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
@endpush

@section('content')
<div class="space-y-6">
    {{-- Search & filters --}}
    <div class="bento-card hero-animate p-6 lg:p-8">
        <form method="GET" action="{{ route('modules.boarding-house.search.index') }}" class="space-y-4">
            <div class="grid lg:grid-cols-12 gap-4">
                <div class="lg:col-span-4">
                    <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Search</label>
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, address, city..." class="input-field">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">City</label>
                    <select name="city" class="input-field">
                        <option value="">All cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Max Price/mo</label>
                    <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="e.g. 100" class="input-field" min="0">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Max Distance (km)</label>
                    <input type="number" name="max_distance" value="{{ $filters['max_distance'] ?? '' }}" placeholder="e.g. 5" class="input-field" min="0" max="50">
                </div>
                <div class="lg:col-span-2 flex items-end">
                    <button type="submit" class="btn-primary w-full">Search</button>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <label class="inline-flex items-center gap-2 font-sans text-sm text-brand-indigo">
                    <input type="checkbox" name="available_only" value="1" @checked($filters['available_only'] ?? false) class="rounded border-brand-lavender text-brand-coral">
                    Available rooms only
                </label>
                <select name="sort" class="input-field w-auto text-sm py-2" onchange="this.form.submit()">
                    <option value="newest" @selected(($filters['sort'] ?? '') === 'newest')>Newest</option>
                    <option value="price_low" @selected(($filters['sort'] ?? '') === 'price_low')>Price: Low to High</option>
                    <option value="price_high" @selected(($filters['sort'] ?? '') === 'price_high')>Price: High to Low</option>
                    <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Top Rated</option>
                    <option value="distance" @selected(($filters['sort'] ?? '') === 'distance')>Nearest to Campus</option>
                </select>
                <a href="{{ route('modules.boarding-house.compare.index') }}" class="font-sans text-sm text-brand-coral hover:underline">
                    Compare ({{ count($compareIds) }}/{{ config('boarding-house.compare_max', 3) }})
                </a>
                <a href="{{ route('modules.boarding-house.favorites.index') }}" class="font-sans text-sm text-brand-coral hover:underline">Wishlist</a>
            </div>
        </form>
    </div>

    {{-- Interactive map --}}
    @if($mapProperties->count())
        <div class="bento-card overflow-hidden p-0 hero-animate">
            <div class="p-4 border-b border-brand-lavender/30 flex justify-between items-center">
                <div>
                    <h3 class="font-heading font-semibold text-brand-indigo">Map Search</h3>
                    <p class="font-sans text-xs text-brand-indigo/60">Click a pin to highlight listing · {{ $campus['name'] }}</p>
                </div>
            </div>
            <div id="browse-map" class="h-80 w-full"
                 data-properties='@json($mapProperties)'
                 data-campus='@json($campus)'
                 data-highlight="{{ $highlightId }}"></div>
        </div>
    @endif

    <p class="font-sans text-sm text-brand-indigo/60 stagger-item">{{ $properties->total() }} properties found</p>

    {{-- Results grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6" id="property-grid">
        @forelse($properties as $property)
            <div id="property-{{ $property->id }}" class="property-card-wrap {{ $highlightId === $property->id ? 'ring-2 ring-brand-coral rounded-3xl' : '' }}">
                @include('modules.boarding-house.partials.property-card', [
                    'property' => $property,
                    'isFavorite' => in_array($property->id, $favoriteIds),
                    'inCompare' => in_array($property->id, $compareIds),
                ])
            </div>
        @empty
            <div class="col-span-full bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo mb-2">No properties match your search</p>
                <p class="font-sans text-brand-indigo/60">Try adjusting your filters or browse all listings.</p>
                <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-secondary mt-4 inline-flex">Clear Filters</a>
            </div>
        @endforelse
    </div>

    <div class="stagger-item">{{ $properties->links() }}</div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const mapEl = document.getElementById('browse-map');
    if (!mapEl) return;

    const properties = JSON.parse(mapEl.dataset.properties || '[]');
    const campus = JSON.parse(mapEl.dataset.campus || '{}');
    const highlightId = parseInt(mapEl.dataset.highlight) || null;

    const center = properties.length
        ? [properties[0].lat, properties[0].lng]
        : [campus.latitude, campus.longitude];

    const map = L.map(mapEl).setView(center, 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }).addTo(map);

    L.marker([campus.latitude, campus.longitude], {
        icon: L.divIcon({ className: 'campus-marker', html: '<div style="background:#4a3f6b;color:white;padding:4px 8px;border-radius:8px;font-size:11px;font-weight:bold;">Campus</div>', iconSize: [60, 24] })
    }).addTo(map).bindPopup(campus.name);

    const cluster = L.markerClusterGroup();
    const markers = {};

    properties.forEach(p => {
        const marker = L.marker([p.lat, p.lng]);
        marker.bindPopup(`<strong>${p.name}</strong><br>$${p.price ?? '—'}/mo<br>${p.distance ?? '—'} km`);
        marker.on('click', () => {
            document.querySelectorAll('.property-card-wrap').forEach(el => el.classList.remove('ring-2', 'ring-brand-coral', 'rounded-3xl'));
            const card = document.getElementById('property-' + p.id);
            if (card) {
                card.classList.add('ring-2', 'ring-brand-coral', 'rounded-3xl');
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            history.replaceState(null, '', '?highlight=' + p.id);
        });
        markers[p.id] = marker;
        cluster.addLayer(marker);
    });
    map.addLayer(cluster);

    if (highlightId && markers[highlightId]) {
        map.setView(markers[highlightId].getLatLng(), 15);
        markers[highlightId].openPopup();
        const card = document.getElementById('property-' + highlightId);
        if (card) card.classList.add('ring-2', 'ring-brand-coral', 'rounded-3xl');
    }
});
</script>
@endpush
@endsection
