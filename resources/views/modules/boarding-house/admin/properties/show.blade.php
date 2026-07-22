@extends('layouts.platform')

@section('title', $property->name)
@section('header', 'Manage: '.$property->name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-3 hero-animate">
        <a href="{{ route('modules.boarding-house.admin.properties.index') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; All Properties</a>
        <a href="{{ route('modules.boarding-house.admin.properties.edit', $property) }}" class="btn-ghost text-sm py-2 px-4 ml-auto">Edit Details</a>
        <a href="{{ route('modules.boarding-house.search.show', $property) }}" class="btn-secondary text-sm py-2 px-4">Public Preview</a>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Rooms list --}}
            <div class="bento-card stagger-item p-6">
                <h3 class="font-heading font-semibold text-brand-indigo mb-4">Rooms ({{ $property->rooms->count() }})</h3>
                @forelse($property->rooms as $room)
                    <div class="border border-brand-lavender/30 rounded-2xl p-4 mb-3">
                        <form method="POST" action="{{ route('modules.boarding-house.admin.properties.rooms.update', [$property, $room]) }}" class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                            @csrf @method('PUT')
                            <div>
                                <label class="text-xs font-sans text-brand-indigo/60">Name</label>
                                <input name="name" value="{{ $room->name }}" class="input-field text-sm py-2" required>
                            </div>
                            <div>
                                <label class="text-xs font-sans text-brand-indigo/60">Type</label>
                                <select name="type" class="input-field text-sm py-2">
                                    @foreach($roomTypes as $type)
                                        <option value="{{ $type }}" @selected($room->type === $type)>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-sans text-brand-indigo/60">Price/mo</label>
                                <input type="number" name="price" value="{{ $room->price }}" class="input-field text-sm py-2" step="0.01" required>
                            </div>
                            <div>
                                <label class="text-xs font-sans text-brand-indigo/60">Capacity</label>
                                <input type="number" name="capacity" value="{{ $room->capacity }}" class="input-field text-sm py-2" min="1" required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="text-xs font-sans text-brand-indigo/60">Description</label>
                                <input name="description" value="{{ $room->description }}" class="input-field text-sm py-2">
                            </div>
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center gap-2 text-sm font-sans">
                                    <input type="checkbox" name="is_available" value="1" @checked($room->is_available) class="rounded text-brand-coral">
                                    Available
                                </label>
                                <button type="submit" class="btn-secondary text-xs py-2 px-3">Save</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('modules.boarding-house.admin.properties.rooms.destroy', [$property, $room]) }}" class="mt-2" onsubmit="return confirm('Delete this room?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-sans text-brand-coral hover:underline">Delete room</button>
                        </form>
                    </div>
                @empty
                    <p class="font-sans text-brand-indigo/50 text-sm">No rooms yet. Add one below.</p>
                @endforelse
            </div>

            {{-- Add room --}}
            <div class="bento-card-accent stagger-item p-6">
                <h3 class="font-heading font-semibold mb-4">Add New Room</h3>
                <form method="POST" action="{{ route('modules.boarding-house.admin.properties.rooms.store', $property) }}" class="grid sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-sans">Room Name *</label>
                        <input name="name" class="input-field" required placeholder="e.g. Room 101">
                    </div>
                    <div>
                        <label class="text-sm font-sans">Type *</label>
                        <select name="type" class="input-field" required>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-sans">Monthly Price *</label>
                        <input type="number" name="price" class="input-field" step="0.01" required>
                    </div>
                    <div>
                        <label class="text-sm font-sans">Capacity *</label>
                        <input type="number" name="capacity" value="1" class="input-field" min="1" required>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-sans">Description</label>
                        <textarea name="description" rows="2" class="input-field"></textarea>
                    </div>
                    <div>
                        <label class="inline-flex items-center gap-2 font-sans text-sm">
                            <input type="checkbox" name="is_available" value="1" checked class="rounded text-brand-coral">
                            Available immediately
                        </label>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="btn-primary">Add Room</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="bento-card-dark stagger-item p-6">
                <h3 class="font-heading font-semibold text-brand-cream mb-2">Quick Stats</h3>
                <dl class="space-y-2 font-sans text-sm">
                    <div class="flex justify-between"><dt class="text-brand-lavender">Status</dt><dd class="text-brand-cream capitalize">{{ $property->status }}</dd></div>
                    <div class="flex justify-between"><dt class="text-brand-lavender">Available</dt><dd class="text-brand-amber">{{ $property->availableRoomsCount() }} rooms</dd></div>
                    <div class="flex justify-between"><dt class="text-brand-lavender">From</dt><dd class="text-brand-cream">${{ number_format($property->minPrice() ?? 0) }}/mo</dd></div>
                    <div class="flex justify-between"><dt class="text-brand-lavender">Rating</dt><dd class="text-brand-cream">{{ $property->averageRating() ? number_format($property->averageRating(), 1).' ★' : 'N/A' }}</dd></div>
                </dl>
            </div>

            <form method="POST" action="{{ route('modules.boarding-house.admin.properties.destroy', $property) }}" onsubmit="return confirm('Delete this property and all its rooms?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-3 rounded-full border-2 border-brand-coral text-brand-coral font-sans text-sm hover:bg-brand-coral hover:text-white transition">Delete Property</button>
            </form>
        </div>
    </div>
</div>
@endsection
