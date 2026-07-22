<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function store(Request $request, Property $property): RedirectResponse
    {
        $this->authorizeProperty($property);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10'],
            'type' => ['required', 'in:single,double,shared,studio'],
            'is_available' => ['boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available', true);
        $property->rooms()->create($validated);

        return back()->with('success', 'Room added successfully.');
    }

    public function update(Request $request, Property $property, Room $room): RedirectResponse
    {
        $this->authorizeProperty($property);
        abort_unless($room->property_id === $property->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'capacity' => ['required', 'integer', 'min:1', 'max:10'],
            'type' => ['required', 'in:single,double,shared,studio'],
            'is_available' => ['boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available');
        $room->update($validated);

        return back()->with('success', 'Room updated.');
    }

    public function destroy(Property $property, Room $room): RedirectResponse
    {
        $this->authorizeProperty($property);
        abort_unless($room->property_id === $property->id, 404);

        $room->delete();

        return back()->with('success', 'Room removed.');
    }

    private function authorizeProperty(Property $property): void
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return;
        }
        abort_unless($property->landlord?->user_id === $user->id, 403);
    }
}
