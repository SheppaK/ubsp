<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\Room;
use App\Models\Modules\BoardingHouse\RoomAvailability;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function show(Property $property, Room $room): View
    {
        abort_unless($room->property_id === $property->id, 404);

        $blocks = RoomAvailability::where('room_id', $room->id)
            ->orderBy('start_date')
            ->get();

        return view('modules.boarding-house.availability.show', [
            'config' => $this->modules->get('boarding-house'),
            'property' => $property,
            'room' => $room,
            'blocks' => $blocks,
        ]);
    }

    public function store(Request $request, Property $property, Room $room): RedirectResponse
    {
        $this->authorizeRoom($property, $room);

        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        RoomAvailability::create([
            ...$validated,
            'room_id' => $room->id,
            'type' => 'blocked',
        ]);

        return back()->with('success', 'Dates blocked on calendar.');
    }

    public function destroy(Property $property, Room $room, RoomAvailability $block): RedirectResponse
    {
        $this->authorizeRoom($property, $room);
        abort_unless($block->room_id === $room->id && $block->type === 'blocked', 403);
        $block->delete();

        return back()->with('success', 'Block removed.');
    }

    private function authorizeRoom(Property $property, Room $room): void
    {
        abort_unless($room->property_id === $property->id, 404);

        $user = auth()->user();
        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return;
        }

        abort_unless($property->landlord?->user_id === $user->id, 403);
    }
}
