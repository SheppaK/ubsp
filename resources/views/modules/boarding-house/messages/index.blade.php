@extends('layouts.platform')

@section('title', 'Messages')
@section('header', 'Booking Messages')

@section('content')
<div class="space-y-6">
    <div class="hero-animate">
        <a href="{{ route('modules.boarding-house.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Module Home</a>
    </div>

    <div class="space-y-3">
        @forelse($conversations as $conversation)
            @php $booking = $conversation->bookingRequest; @endphp
            <a href="{{ route('modules.boarding-house.messages.show', $conversation) }}" class="bento-card stagger-item p-5 block hover:border-brand-coral/40 transition" data-hover-lift>
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <h3 class="font-heading font-semibold text-brand-indigo">{{ $booking->room->property->name }}</h3>
                        <p class="font-sans text-sm text-brand-indigo/60">{{ $booking->room->name }} · {{ $booking->user->name }}</p>
                        @if($conversation->messages->last())
                            <p class="font-sans text-xs text-brand-indigo/50 mt-2 truncate max-w-md">{{ $conversation->messages->last()->body }}</p>
                        @endif
                    </div>
                    <span class="tag text-xs capitalize">{{ $booking->status }}</span>
                </div>
            </a>
        @empty
            <div class="bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo">No conversations yet</p>
                <p class="font-sans text-brand-indigo/60 mt-2">Messages appear when you submit or receive booking requests.</p>
            </div>
        @endforelse
    </div>

    {{ $conversations->links() }}
</div>
@endsection
