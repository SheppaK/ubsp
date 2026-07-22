@extends('layouts.platform')

@section('title', 'Payment Successful')
@section('header', 'Payment Status')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bento-card text-center p-10 hero-animate">
        @if($payment->isPaid())
            <div class="text-5xl mb-4">✓</div>
            <h2 class="font-heading text-2xl font-bold text-brand-indigo">Payment Received</h2>
            <p class="font-sans text-brand-indigo/70 mt-2">Your holding fee of ${{ number_format($payment->amount, 2) }} has been processed.</p>
        @else
            <h2 class="font-heading text-2xl font-bold text-brand-indigo">Payment Pending</h2>
            <p class="font-sans text-brand-indigo/70 mt-2">We could not confirm your payment yet. Try again or contact support.</p>
            <a href="{{ route('modules.boarding-house.payments.checkout', $payment) }}" class="btn-primary mt-4 inline-flex">Retry Payment</a>
        @endif
        <a href="{{ route('modules.boarding-house.bookings.mine') }}" class="btn-secondary mt-6 inline-flex">View My Bookings</a>
    </div>
</div>
@endsection
