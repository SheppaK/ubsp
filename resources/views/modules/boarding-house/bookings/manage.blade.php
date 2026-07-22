@extends('layouts.platform')

@section('title', 'Booking Inbox')
@section('header', 'Manage Booking Requests')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
        <div class="flex gap-2">
            @foreach(['', 'pending', 'approved', 'rejected'] as $status)
                <a href="{{ route('modules.boarding-house.admin.bookings.manage', $status ? ['status' => $status] : []) }}"
                   class="px-4 py-2 rounded-full text-sm font-sans transition {{ ($statusFilter ?? '') === $status ? 'bg-brand-indigo text-brand-cream' : 'bg-brand-lavender/30 text-brand-indigo hover:bg-brand-lavender/50' }}">
                    {{ $status ? ucfirst($status) : 'All' }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bento-card stagger-item p-6">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <h3 class="font-heading font-semibold text-brand-indigo">{{ $booking->room->property->name }}</h3>
                            <span class="tag text-xs capitalize">{{ $booking->status }}</span>
                        </div>
                        <p class="font-sans text-sm text-brand-indigo/70">
                            <strong>{{ $booking->user->name }}</strong> ({{ $booking->user->email }}) wants
                            <strong>{{ $booking->room->name }}</strong> at ${{ number_format($booking->room->price) }}/mo
                        </p>
                        <p class="font-sans text-xs text-brand-indigo/50 mt-1">
                            Move-in: {{ $booking->move_in_date?->format('M d, Y') }} · Duration: {{ $booking->duration_months }} mo · Requested {{ $booking->created_at->diffForHumans() }}
                        </p>
                        @if($booking->message)
                            <blockquote class="mt-3 pl-4 border-l-4 border-brand-lavender font-sans text-sm text-brand-indigo/70 italic">"{{ $booking->message }}"</blockquote>
                        @endif
                        <a href="{{ route('modules.boarding-house.messages.booking', $booking) }}" class="text-xs font-sans text-brand-coral hover:underline mt-2 inline-block">Open Chat</a>
                    </div>
                    @if($booking->isPending())
                        <div class="flex gap-2 shrink-0">
                            <form method="POST" action="{{ route('modules.boarding-house.admin.bookings.approve', $booking) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-secondary text-sm py-2 px-4">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('modules.boarding-house.admin.bookings.reject', $booking) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-4 py-2 rounded-full text-sm font-sans border-2 border-brand-coral text-brand-coral hover:bg-brand-coral hover:text-white transition">Reject</button>
                            </form>
                        </div>
                    @elseif($booking->responded_at)
                        <p class="text-xs font-sans text-brand-indigo/40">Responded {{ $booking->responded_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo">No booking requests</p>
                <p class="font-sans text-brand-indigo/60 mt-2">Requests from students will appear here.</p>
            </div>
        @endforelse
    </div>

    {{ $bookings->links() }}
</div>
@endsection
