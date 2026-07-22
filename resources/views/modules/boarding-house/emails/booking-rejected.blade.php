<x-mail::message>
# Booking Not Approved

We're sorry — your booking request for **{{ $booking->room->property->name }}** ({{ $booking->room->name }}) was not approved at this time.

You can browse other available properties and submit a new request.

<x-mail::button :url="route('modules.boarding-house.search.index')">
Browse Properties
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
