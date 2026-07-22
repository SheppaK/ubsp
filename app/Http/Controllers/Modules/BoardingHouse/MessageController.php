<?php

namespace App\Http\Controllers\Modules\BoardingHouse;

use App\Http\Controllers\Controller;
use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Conversation;
use App\Models\Modules\BoardingHouse\Message;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function index(): View
    {
        $conversations = Conversation::with(['bookingRequest.room.property', 'bookingRequest.user', 'messages'])
            ->whereHas('bookingRequest', function ($q) {
                $userId = auth()->id();
                $q->where('user_id', $userId)
                    ->orWhereHas('room.property.landlord', fn ($lq) => $lq->where('user_id', $userId));
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('modules.boarding-house.messages.index', [
            'config' => $this->modules->get('boarding-house'),
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation): View
    {
        $this->authorizeConversation($conversation);

        $conversation->load(['bookingRequest.room.property.landlord.user', 'bookingRequest.user', 'messages.user']);

        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('modules.boarding-house.messages.show', [
            'config' => $this->modules->get('boarding-house'),
            'conversation' => $conversation,
        ]);
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'body' => $validated['body'],
        ]);

        $conversation->touch();

        return back()->with('success', 'Message sent.');
    }

    public function forBooking(BookingRequest $booking): RedirectResponse
    {
        abort_unless(
            $booking->user_id === auth()->id()
            || $booking->room?->property?->landlord?->user_id === auth()->id()
            || auth()->user()->hasAnyRole(['super-admin', 'administrator']),
            403
        );

        $conversation = Conversation::firstOrCreate(['booking_request_id' => $booking->id]);

        return redirect()->route('modules.boarding-house.messages.show', $conversation);
    }

    private function authorizeConversation(Conversation $conversation): void
    {
        $booking = $conversation->bookingRequest;
        $user = auth()->user();

        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return;
        }

        abort_unless(
            $booking->user_id === $user->id
            || $booking->room?->property?->landlord?->user_id === $user->id,
            403
        );
    }
}
