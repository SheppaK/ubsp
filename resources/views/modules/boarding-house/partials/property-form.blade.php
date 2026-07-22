@php $p = $property; @endphp

<div class="grid sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Property Name *</label>
        <input type="text" name="name" value="{{ old('name', $p?->name) }}" class="input-field" required>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Address *</label>
        <input type="text" name="address" value="{{ old('address', $p?->address) }}" class="input-field" required>
    </div>
    <div>
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">City *</label>
        <input type="text" name="city" value="{{ old('city', $p?->city) }}" class="input-field" required>
    </div>
    <div>
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Distance to Campus (km)</label>
        <input type="number" name="distance_to_campus_km" value="{{ old('distance_to_campus_km', $p?->distance_to_campus_km) }}" class="input-field" min="0" max="50">
    </div>
    <div>
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Latitude</label>
        <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $p?->latitude) }}" class="input-field" placeholder="-17.7833">
    </div>
    <div>
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Longitude</label>
        <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $p?->longitude) }}" class="input-field" placeholder="31.0333">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Description</label>
        <textarea name="description" rows="4" class="input-field">{{ old('description', $p?->description) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Status *</label>
        <select name="status" class="input-field" required>
            @foreach(['draft', 'published', 'archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', $p?->status ?? 'draft') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Cover Image</label>
        <input type="file" name="cover_image" accept="image/*" class="input-field py-2">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Gallery Images</label>
        <input type="file" name="gallery[]" accept="image/*" multiple class="input-field py-2">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-2">Amenities</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach($amenityOptions as $amenity)
                <label class="inline-flex items-center gap-2 font-sans text-sm text-brand-indigo">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                        @checked(in_array($amenity, old('amenities', $p?->amenities ?? [])))
                        class="rounded border-brand-lavender text-brand-coral">
                    {{ $amenity }}
                </label>
            @endforeach
        </div>
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Virtual Tour Video URL</label>
        <input type="url" name="virtual_tour_video_url" value="{{ old('virtual_tour_video_url', $p?->virtual_tour_video_url) }}" class="input-field" placeholder="https://youtube.com/embed/...">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">360° Panorama URL</label>
        <input type="url" name="virtual_tour_360_url" value="{{ old('virtual_tour_360_url', $p?->virtual_tour_360_url) }}" class="input-field" placeholder="https://... embed URL">
    </div>
</div>
