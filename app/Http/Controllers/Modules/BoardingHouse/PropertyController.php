<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Landlord;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\PropertyImage;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function index(): View
    {
        $user = auth()->user();
        $query = Property::with(['rooms', 'landlord.user', 'images'])->latest();

        if (! $user->hasAnyRole(['super-admin', 'administrator'])) {
            $landlord = Landlord::where('user_id', $user->id)->first();
            $query->where('landlord_id', $landlord?->id ?? 0);
        }

        return view('modules.boarding-house.admin.properties.index', [
            'config' => $this->modules->get('boarding-house'),
            'properties' => $query->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('modules.boarding-house.admin.properties.create', [
            'config' => $this->modules->get('boarding-house'),
            'amenityOptions' => $this->amenityOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'distance_to_campus_km' => ['nullable', 'integer', 'min:0', 'max:50'],
            'status' => ['required', 'in:draft,published,archived'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery.*' => ['nullable', 'image', 'max:5120'],
            'virtual_tour_video_url' => ['nullable', 'url', 'max:500'],
            'virtual_tour_360_url' => ['nullable', 'url', 'max:500'],
        ]);

        $landlord = Landlord::forUser($request->user());
        unset($validated['cover_image'], $validated['gallery']);

        $property = $landlord->properties()->create($validated);

        $this->handleImages($request, $property);

        return redirect()
            ->route('modules.boarding-house.admin.properties.show', $property)
            ->with('success', 'Property created. Add rooms to start receiving bookings.');
    }

    public function show(Property $property): View
    {
        $this->authorizeProperty($property);

        $property->load(['rooms', 'images', 'reviews.user', 'landlord.user']);

        return view('modules.boarding-house.admin.properties.show', [
            'config' => $this->modules->get('boarding-house'),
            'property' => $property,
            'roomTypes' => ['single', 'double', 'shared', 'studio'],
        ]);
    }

    public function edit(Property $property): View
    {
        $this->authorizeProperty($property);

        return view('modules.boarding-house.admin.properties.edit', [
            'config' => $this->modules->get('boarding-house'),
            'property' => $property,
            'amenityOptions' => $this->amenityOptions(),
        ]);
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $this->authorizeProperty($property);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string'],
            'distance_to_campus_km' => ['nullable', 'integer', 'min:0', 'max:50'],
            'status' => ['required', 'in:draft,published,archived'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string'],
            'cover_image' => ['nullable', 'image', 'max:5120'],
            'gallery.*' => ['nullable', 'image', 'max:5120'],
            'virtual_tour_video_url' => ['nullable', 'url', 'max:500'],
            'virtual_tour_360_url' => ['nullable', 'url', 'max:500'],
        ]);

        unset($validated['cover_image'], $validated['gallery']);
        $property->update($validated);
        $this->handleImages($request, $property);

        return redirect()
            ->route('modules.boarding-house.admin.properties.show', $property)
            ->with('success', 'Property updated successfully.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorizeProperty($property);

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        if ($property->cover_image) {
            Storage::disk('public')->delete($property->cover_image);
        }

        $property->delete();

        return redirect()
            ->route('modules.boarding-house.admin.properties.index')
            ->with('success', 'Property deleted.');
    }

    private function authorizeProperty(Property $property): void
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return;
        }
        abort_unless($property->landlord?->user_id === $user->id, 403);
    }

    private function handleImages(Request $request, Property $property): void
    {
        if ($request->hasFile('cover_image')) {
            if ($property->cover_image) {
                Storage::disk('public')->delete($property->cover_image);
            }
            $property->update([
                'cover_image' => $request->file('cover_image')->store('boarding-house/covers', 'public'),
            ]);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $file) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'path' => $file->store('boarding-house/gallery', 'public'),
                    'sort_order' => $i,
                ]);
            }
        }
    }

    private function amenityOptions(): array
    {
        return [
            'WiFi', 'Electricity Included', 'Water Included', 'Parking',
            'Security', 'Furnished', 'Kitchen', 'Laundry', 'Study Desk',
            'Air Conditioning', 'Backup Power', 'CCTV',
        ];
    }
}
