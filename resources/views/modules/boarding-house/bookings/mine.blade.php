@extends('layouts.platform')

@section('title', 'My Bookings')
@section('header', 'My Booking Requests')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex items-center justify-between">
        <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
        <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-primary text-sm">Find Properties</a>
    </div>

    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bento-card stagger-item p-6" data-hover-lift>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-heading font-semibold text-brand-indigo">{{ $booking->room->property->name }}</h3>
                        <p class="font-sans text-sm text-brand-indigo/60">{{ $booking->room->name }} · {{ $booking->room->typeLabel() }} · ${{ number_format($booking->room->price) }}/mo</p>
                        <p class="font-sans text-xs text-brand-indigo/50 mt-1">
                            Move-in: {{ $booking->move_in_date?->format('M d, Y') ?? 'TBD' }} · {{ $booking->duration_months }} month(s)
                        </p>
                        @if($booking->message)
                            <p class="font-sans text-sm text-brand-indigo/70 mt-2 italic">"{{ $booking->message }}"</p>
                        @endif
                        <div class="flex flex-wrap gap-2 mt-3">
                            <a href="{{ route('modules.boarding-house.messages.booking', $booking) }}" class="text-xs font-sans text-brand-coral hover:underline">Open Chat</a>
                            @if($booking->lease_pdf_path)
                                <a href="{{ asset('storage/'.$booking->lease_pdf_path) }}" target="_blank" class="text-xs font-sans text-brand-coral hover:underline">Download Lease PDF</a>
                            @endif
                            @if($booking->latestPayment && $booking->payment_status === 'pending')
                                <a href="{{ route('modules.boarding-house.payments.checkout', $booking->latestPayment) }}" class="text-xs font-sans text-brand-coral hover:underline font-medium">Pay Holding Fee</a>
                            @elseif($booking->payment_status === 'paid')
                                <span class="text-xs font-sans text-brand-indigo/50">Deposit paid ✓</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        @php
                            $statusColors = [
                                'pending' => 'bg-brand-amber/30 text-brand-indigo',
                                'approved' => 'bg-brand-lavender/50 text-brand-indigo',
                                'rejected' => 'bg-brand-coral/20 text-brand-coral',
                            ];
                        @endphp
                        <span class="px-4 py-1.5 rounded-full text-sm font-sans font-medium capitalize {{ $statusColors[$booking->status] ?? '' }}">
                            {{ $booking->status }}
                        </span>
                        <p class="text-xs font-sans text-brand-indigo/40 mt-2">Submitted {{ $booking->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo mb-2">No bookings yet</p>
                <p class="font-sans text-brand-indigo/60 mb-4">Browse boarding houses and send your first booking request.</p>
                <a href="{{ route('modules.boarding-house.search.index') }}" class="btn-primary">Browse Properties</a>
            </div>
        @endforelse
    </div>

    {{ $bookings->links() }}
</div>
@endsection
