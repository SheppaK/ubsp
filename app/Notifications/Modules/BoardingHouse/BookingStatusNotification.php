<?php

namespace App\Notifications\Modules\BoardingHouse;

use App\Models\Modules\BoardingHouse\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public BookingRequest $booking,
        public string $event,
    ) {}

    public function via(object $notifiable): array
    {
        return ['twilio_sms'];
    }

    public function toTwilioSms(object $notifiable): string
    {
        $property = $this->booking->room->property->name;

        return match ($this->event) {
            'submitted' => "UBSP: Your booking request for {$property} was submitted.",
            'approved' => "UBSP: Great news! Your booking for {$property} was approved. Check email for lease & payment.",
            'rejected' => "UBSP: Your booking request for {$property} was not approved.",
            default => "UBSP: Booking update for {$property}.",
        };
    }
}
