@extends('layouts.platform')

@section('title', 'My Appointments')
@section('header', 'My Appointments')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex justify-between items-center">
        <a href="{{ route('modules.clinic.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Clinic Home</a>
        <a href="{{ route('modules.clinic.appointments.create') }}" class="btn-primary text-sm">Book New</a>
    </div>

    <div class="space-y-4">
        @forelse($appointments as $appointment)
            <div class="bento-card stagger-item p-6" data-hover-lift>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h3 class="font-heading font-semibold text-brand-indigo">{{ $appointment->reason }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-sans {{ $appointment->statusColor() }}">{{ $appointment->statusLabel() }}</span>
                        </div>
                        <p class="font-sans text-sm text-brand-indigo/70">
                            Dr. {{ $appointment->doctor->user->name }} · {{ $appointment->doctor->specialization }}
                        </p>
                        <p class="font-sans text-sm text-brand-indigo/50 mt-1">
                            {{ $appointment->scheduled_at->format('l, M d, Y \a\t H:i') }}
                        </p>
                        @if($appointment->patient_notes)
                            <p class="font-sans text-sm text-brand-indigo/60 mt-2 italic">"{{ $appointment->patient_notes }}"</p>
                        @endif
                        @if($appointment->provider_notes && $appointment->status === 'rejected')
                            <p class="font-sans text-sm text-brand-coral mt-2">Provider note: {{ $appointment->provider_notes }}</p>
                        @endif
                    </div>
                    @if(in_array($appointment->status, ['pending', 'confirmed', 'scheduled']))
                        <form method="POST" action="{{ route('modules.clinic.appointments.cancel', $appointment) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-sm font-sans text-brand-coral hover:underline" onclick="return confirm('Cancel this appointment?')">Cancel</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo mb-2">No appointments yet</p>
                <p class="font-sans text-brand-indigo/60 mb-4">Book your first appointment with a healthcare provider.</p>
                <a href="{{ route('modules.clinic.appointments.create') }}" class="btn-primary">Book Appointment</a>
            </div>
        @endforelse
    </div>

    {{ $appointments->links() }}
</div>
@endsection
