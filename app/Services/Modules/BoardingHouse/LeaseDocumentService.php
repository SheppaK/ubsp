<?php

namespace App\Services\Modules\BoardingHouse;

use App\Models\Modules\BoardingHouse\BookingRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class LeaseDocumentService
{
    public function generate(BookingRequest $booking): string
    {
        $booking->load(['room.property.landlord.user', 'user']);

        $pdf = Pdf::loadView('modules.boarding-house.pdf.lease-agreement', [
            'booking' => $booking,
            'property' => $booking->room->property,
            'room' => $booking->room,
            'tenant' => $booking->user,
            'landlord' => $booking->room->property->landlord,
        ]);

        $path = 'boarding-house/leases/lease-'.$booking->id.'-'.now()->format('YmdHis').'.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $booking->update(['lease_pdf_path' => $path]);

        return $path;
    }

    public function url(BookingRequest $booking): ?string
    {
        return $booking->lease_pdf_path
            ? asset('storage/'.$booking->lease_pdf_path)
            : null;
    }
}
