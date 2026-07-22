<x-mail::message>
# Booking Approved!

Great news{{ $forTenant ? '' : ' — tenant notified' }}! Your booking for **{{ $booking->room->property->name }}** has been approved.

**Room:** {{ $booking->room->name }}  
**Move-in:** {{ $booking->move_in_date?->format('M d, Y') }}  
**Monthly rent:** ${{ number_format($booking->room->price, 2) }}

@if($booking->lease_pdf_path)
Your rental agreement PDF is attached to your account. Download it from your bookings page.
@endif

@if($forTenant && $payment)
<x-mail::button :url="route('modules.boarding-house.payments.checkout', $payment)">
Pay Holding Fee (${{ number_format($payment->amount, 2) }})
</x-mail::button>
@endif

<x-mail::button :url="route('modules.boarding-house.messages.booking', $booking)">
Open Chat with Landlord
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
