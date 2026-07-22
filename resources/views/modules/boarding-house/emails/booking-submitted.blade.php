<x-mail::message>
# @if($forTenant) Booking Request Submitted @else New Booking Request @endif

**Property:** {{ $booking->room->property->name }}  
**Room:** {{ $booking->room->name }} ({{ $booking->room->typeLabel() }})  
**Move-in:** {{ $booking->move_in_date?->format('M d, Y') }}  
**Duration:** {{ $booking->duration_months }} month(s)

@if(!$forTenant)
**Tenant:** {{ $booking->user->name }} ({{ $booking->user->email }})
@endif

@if($booking->message)
> {{ $booking->message }}
@endif

<x-mail::button :url="route('modules.boarding-house.bookings.mine')">
View My Bookings
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
