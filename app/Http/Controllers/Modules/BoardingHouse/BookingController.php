<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Property;
use App\Models\Modules\BoardingHouse\Room;
use App\Services\ModuleManager;
use App\Services\Modules\BoardingHouse\BookingWorkflowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected ModuleManager $modules,
        protected BookingWorkflowService $workflow,
    ) {}

    public function store(Request $request, Property $property, Room $room): RedirectResponse
    {
        abort_unless($property->status === 'published', 404);
        abort_unless($room->property_id === $property->id && $room->is_available, 422);

        $validated = $request->validate([
            'move_in_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'min:1', 'max:24'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $existing = BookingRequest::where('room_id', $room->id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->withErrors(['room' => 'You already have a pending booking for this room.']);
        }

        $booking = BookingRequest::create([
            ...$validated,
            'room_id' => $room->id,
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        $this->workflow->onSubmitted($booking);

        return redirect()
            ->route('modules.boarding-house.bookings.mine')
            ->with('success', 'Booking request submitted! The landlord will respond soon.');
    }

    public function mine(): View
    {
        $bookings = BookingRequest::with(['room.property.landlord.user', 'conversation', 'latestPayment'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('modules.boarding-house.bookings.mine', [
            'config' => $this->modules->get('boarding-house'),
            'bookings' => $bookings,
        ]);
    }

    public function manage(Request $request): View
    {
        $user = auth()->user();
        $query = BookingRequest::with(['room.property', 'user', 'conversation'])->latest();

        if (! $user->hasAnyRole(['super-admin', 'administrator'])) {
            $query->whereHas('room.property.landlord', fn ($q) => $q->where('user_id', $user->id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('modules.boarding-house.bookings.manage', [
            'config' => $this->modules->get('boarding-house'),
            'bookings' => $query->paginate(15)->withQueryString(),
            'statusFilter' => $request->input('status'),
        ]);
    }

    public function approve(BookingRequest $booking): RedirectResponse
    {
        $this->authorizeBookingAction($booking);
        abort_unless($booking->isPending(), 422);

        $booking->update([
            'status' => 'approved',
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        $this->workflow->onApproved($booking->fresh());

        return back()->with('success', 'Booking approved. Lease generated and tenant notified.');
    }

    public function reject(BookingRequest $booking): RedirectResponse
    {
        $this->authorizeBookingAction($booking);
        abort_unless($booking->isPending(), 422);

        $booking->update([
            'status' => 'rejected',
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        $this->workflow->onRejected($booking->fresh());

        return back()->with('success', 'Booking rejected.');
    }

    private function authorizeBookingAction(BookingRequest $booking): void
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return;
        }

        abort_unless(
            $booking->room?->property?->landlord?->user_id === $user->id,
            403
        );
    }
}
