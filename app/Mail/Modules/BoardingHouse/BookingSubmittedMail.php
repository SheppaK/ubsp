<?php

namespace App\Mail\Modules\BoardingHouse;

use App\Models\Modules\BoardingHouse\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookingRequest $booking,
        public bool $forTenant = false,
    ) {}

    public function envelope(): Envelope
    {
        $property = $this->booking->room->property->name;

        return new Envelope(
            subject: $this->forTenant
                ? "Booking request submitted — {$property}"
                : "New booking request — {$property}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'modules.boarding-house.emails.booking-submitted',
        );
    }
}
