<?php

namespace App\Mail\Modules\BoardingHouse;

use App\Models\Modules\BoardingHouse\BookingRequest;
use App\Models\Modules\BoardingHouse\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public BookingRequest $booking,
        public ?Payment $payment = null,
        public bool $forTenant = true,
    ) {}

    public function envelope(): Envelope
    {
        $property = $this->booking->room->property->name;

        return new Envelope(
            subject: "Booking approved — {$property}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'modules.boarding-house.emails.booking-approved',
        );
    }
}
