@extends('layouts.platform')

@section('title', 'Compare Properties')
@section('header', 'Compare Properties')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.boarding-house.search.index') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Back to browse</a>
        @if($properties->count())
            <form method="POST" action="{{ route('modules.boarding-house.compare.clear') }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn-secondary text-sm">Clear All</button>
            </form>
        @endif
    </div>

    @if($properties->isEmpty())
        <div class="bento-card text-center py-16 stagger-item">
            <p class="font-heading text-xl text-brand-indigo mb-2">Nothing to compare</p>
            <p class="font-sans text-brand-indigo/60 mb-4">Add up to {{ config('boarding-house.compare_max', 3) }} properties from browse listings.</p>
            <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-primary">Browse Properties</a>
        </div>
    @else
        <div class="overflow-x-auto stagger-item">
            <table class="w-full min-w-[640px] bento-card">
                <thead>
                    <tr class="border-b border-brand-lavender/30">
                        <th class="p-4 text-left font-heading text-brand-indigo w-36">Feature</th>
                        @foreach($properties as $property)
                            <th class="p-4 text-left font-heading text-brand-indigo">
                                <a href="{{ route('modules.boarding-house.search.show', $property) }}" class="hover:text-brand-coral">{{ $property->name }}</a>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="font-sans text-sm">
                    @php
                        $rows = [
                            'City' => fn($p) => $p->city,
                            'Min Price/mo' => fn($p) => '$'.number_format($p->minPrice() ?? 0),
                            'Available Rooms' => fn($p) => $p->availableRoomsCount(),
                            'Distance to Campus' => fn($p) => ($p->computedDistanceKm() ?? '—').' km',
                            'Rating' => fn($p) => $p->averageRating() ? number_format($p->averageRating(), 1).' ★' : '—',
                            'Amenities' => fn($p) => implode(', ', array_slice($p->amenities ?? [], 0, 5)) ?: '—',
                            'Virtual Tour' => fn($p) => $p->hasVirtualTour() ? 'Yes' : 'No',
                        ];
                    @endphp
                    @foreach($rows as $label => $fn)
                        <tr class="border-b border-brand-lavender/20">
                            <td class="p-4 font-medium text-brand-indigo/70">{{ $label }}</td>
                            @foreach($properties as $property)
                                <td class="p-4 text-brand-indigo">{{ $fn($property) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
