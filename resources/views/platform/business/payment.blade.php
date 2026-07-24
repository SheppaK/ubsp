@extends('layouts.platform')

@section('title', 'Complete Payment')
@section('header', 'Unlock Your Modules')

@section('content')
<div class="max-w-xl space-y-6">
    @if(session('error'))
        <div class="bento-card p-4 border-brand-coral/30 bg-brand-coral/5 font-sans text-brand-coral">{{ session('error') }}</div>
    @endif
    @if(session('status'))
        <div class="bento-card p-4 border-brand-amber/40 bg-brand-amber/10 font-sans text-brand-indigo">{{ session('status') }}</div>
    @endif

    <div class="bento-card p-8 space-y-5 hero-animate">
        <div>
            <h2 class="font-heading text-xl font-bold text-brand-indigo">Payment required</h2>
            <p class="font-sans text-sm text-brand-indigo/60 mt-1">
                Your business <span class="font-semibold text-brand-indigo">{{ $business->name }}</span> is registered,
                but modules stay locked until payment is completed.
            </p>
        </div>

        <div>
            <p class="text-xs uppercase tracking-wide text-brand-indigo/50 font-sans mb-2">Selected modules</p>
            <ul class="space-y-2">
                @foreach($pricedModules as $mod)
                    <li class="flex justify-between font-sans text-sm">
                        <span class="text-brand-indigo">{{ $mod->name }}</span>
                        <span class="font-medium text-brand-indigo">K {{ number_format((float) $mod->price_zmw, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="pt-3 border-t border-brand-lavender/30 flex justify-between items-center">
            <span class="font-heading font-bold text-brand-indigo">Total due</span>
            <span class="font-heading text-2xl font-bold text-brand-coral">K {{ number_format((float) $payment->amount_zmw, 2) }} ZMW</span>
        </div>

        <p class="text-xs text-brand-indigo/50 font-sans">Reference: {{ $payment->seller_reference }}</p>

        @if($simulating)
            <div class="p-4 rounded-2xl bg-brand-amber/15 font-sans text-sm text-brand-indigo">
                <strong>Local simulation is ON.</strong> No USSD prompt will be sent to your phone.
                Turn off “Simulate payments locally” in KC Pay Settings if you want a real mobile money prompt.
            </div>
        @elseif(! $kcpayReady)
            <div class="p-4 rounded-2xl bg-brand-coral/10 font-sans text-sm text-brand-coral">
                KC Pay is not configured. Please contact the platform administrator.
            </div>
        @endif

        @if($kcpayReady)
            <a href="{{ route('register.business.checkout', $payment) }}" class="btn-primary inline-flex w-full justify-center">
                Pay now — K {{ number_format((float) $payment->amount_zmw, 2) }}
            </a>
        @endif
    </div>
</div>
@endsection
