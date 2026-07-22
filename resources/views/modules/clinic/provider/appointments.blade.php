@extends('layouts.platform')

@section('title', 'Appointment Inbox')
@section('header', 'Manage Appointment Requests')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.clinic.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Clinic Home</a>
        <div class="flex gap-2">
            @foreach(['', 'pending', 'confirmed', 'completed', 'rejected', 'cancelled'] as $status)
                <a href="{{ route('modules.clinic.provider.appointments', $status ? ['status' => $status] : []) }}"
                   class="px-3 py-1.5 rounded-full text-xs font-sans transition {{ ($statusFilter ?? '') === $status ? 'bg-brand-indigo text-brand-cream' : 'bg-brand-lavender/30 text-brand-indigo' }}">
                    {{ $status ? ucfirst($status) : 'All' }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="space-y-4">
        @forelse($appointments as $appointment)
            <div class="bento-card stagger-item p-6">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <h3 class="font-heading font-semibold text-brand-indigo">{{ $appointment->reason }}</h3>
                            <span class="px-3 py-1 rounded-full text-xs font-sans {{ $appointment->statusColor() }}">{{ $appointment->statusLabel() }}</span>
                        </div>
                        <p class="font-sans text-sm text-brand-indigo/70">
                            <strong>{{ $appointment->patient->fullName() }}</strong>
                            @if($appointment->patient->phone) · {{ $appointment->patient->phone }} @endif
                        </p>
                        @if($appointment->patient->allergies)
                            <p class="text-xs font-sans text-brand-coral mt-1">⚠ Allergies: {{ $appointment->patient->allergies }}</p>
                        @endif
                        <p class="font-sans text-sm text-brand-indigo/50 mt-2">
                            Requested: {{ $appointment->scheduled_at->format('M d, Y H:i') }}
                            · Dr. {{ $appointment->doctor->user->name }}
                        </p>
                        @if($appointment->patient_notes)
                            <blockquote class="mt-3 pl-4 border-l-4 border-brand-lavender font-sans text-sm text-brand-indigo/70 italic">"{{ $appointment->patient_notes }}"</blockquote>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2 shrink-0">
                        <a href="{{ route('modules.clinic.provider.patients.show', $appointment->patient) }}" class="btn-ghost text-xs py-2 text-center">View Patient</a>
                        @if($appointment->isAwaitingApproval())
                            <form method="POST" action="{{ route('modules.clinic.provider.appointments.confirm', $appointment) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-secondary w-full text-sm py-2">Confirm</button>
                            </form>
                            <form method="POST" action="{{ route('modules.clinic.provider.appointments.reject', $appointment) }}" class="space-y-2">
                                @csrf @method('PATCH')
                                <input type="text" name="provider_notes" placeholder="Reason (optional)" class="input-field text-xs py-1">
                                <button type="submit" class="w-full py-2 rounded-full text-sm font-sans border-2 border-brand-coral text-brand-coral hover:bg-brand-coral hover:text-white transition">Decline</button>
                            </form>
                        @elseif(in_array($appointment->status, ['confirmed', 'scheduled']))
                            <form method="POST" action="{{ route('modules.clinic.provider.appointments.complete', $appointment) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-accent w-full text-sm py-2">Mark Completed</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bento-card text-center py-16 stagger-item">
                <p class="font-heading text-xl text-brand-indigo">No appointment requests</p>
                <p class="font-sans text-brand-indigo/60 mt-2">Patient requests will appear here.</p>
            </div>
        @endforelse
    </div>

    {{ $appointments->links() }}
</div>
@endsection
