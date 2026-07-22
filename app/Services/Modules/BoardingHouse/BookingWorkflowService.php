<?php

namespace App\Services\Modules\BoardingHouse;

use App\Mail\Modules\BoardingHouse\BookingApprovedMail;
use App\Mail\Modules\BoardingHouse\BookingRejectedMail;
use App\Mail\Modules\BoardingHouse\BookingSubmittedMail;
use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Conversation;
use App\Models\Modules\BoardingHouse\RoomAvailability;
use App\Notifications\Modules\BoardingHouse\BookingStatusNotification;
use Illuminate\Support\Facades\Mail;

class BookingWorkflowService
{
    public function __construct(
        protected LeaseDocumentService $leaseDocuments,
        protected StripePaymentService $payments,
    ) {}

    public function onSubmitted(BookingRequest $booking): void
    {
        $booking->load(['room.property.landlord.user', 'user']);

        Conversation::firstOrCreate(['booking_request_id' => $booking->id]);

        $landlordEmail = $booking->room->property->landlord?->user?->email;
        if ($landlordEmail) {
            Mail::to($landlordEmail)->send(new BookingSubmittedMail($booking));
        }

        Mail::to($booking->user->email)->send(new BookingSubmittedMail($booking, forTenant: true));

        $this->notifySms($booking, 'submitted');
    }

    public function onApproved(BookingRequest $booking): void
    {
        $booking->load(['room.property.landlord.user', 'user']);

        $this->leaseDocuments->generate($booking);
        $payment = $this->payments->createCheckoutSession($booking);

        RoomAvailability::create([
            'room_id' => $booking->room_id,
            'start_date' => $booking->move_in_date,
            'end_date' => $booking->move_in_date->copy()->addMonths($booking->duration_months),
            'type' => 'booked',
            'booking_request_id' => $booking->id,
            'note' => 'Approved booking #'.$booking->id,
        ]);

        $booking->room->update(['is_available' => false]);

        Mail::to($booking->user->email)->send(new BookingApprovedMail($booking, $payment));

        $landlordEmail = $booking->room->property->landlord?->user?->email;
        if ($landlordEmail) {
            Mail::to($landlordEmail)->send(new BookingApprovedMail($booking, $payment, forTenant: false));
        }

        $this->notifySms($booking, 'approved');
    }

    public function onRejected(BookingRequest $booking): void
    {
        $booking->load(['room.property.landlord.user', 'user']);

        Mail::to($booking->user->email)->send(new BookingRejectedMail($booking));

        $this->notifySms($booking, 'rejected');
    }

    private function notifySms(BookingRequest $booking, string $event): void
    {
        $booking->user->notify(new BookingStatusNotification($booking, $event));
    }
}
