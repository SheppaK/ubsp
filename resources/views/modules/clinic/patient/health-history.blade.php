@extends('layouts.platform')

@section('title', 'Health History')
@section('header', 'My Health History')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.clinic.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Clinic Home</a>
        <a href="{{ route('modules.clinic.appointments.create') }}" class="btn-primary text-sm">Book Appointment</a>
    </div>

    {{-- Patient summary --}}
    <div class="bento-card-dark stagger-item p-6">
        <div class="flex flex-wrap gap-6">
            <div>
                <p class="text-xs font-sans text-brand-lavender uppercase">Patient</p>
                <p class="font-heading text-xl font-bold text-brand-cream">{{ $patient->fullName() }}</p>
            </div>
            @if($patient->blood_type)
                <div><p class="text-xs text-brand-lavender">Blood Type</p><p class="font-heading text-brand-cream">{{ $patient->blood_type }}</p></div>
            @endif
            @if($patient->allergies)
                <div><p class="text-xs text-brand-lavender">Allergies</p><p class="font-sans text-brand-coral">{{ $patient->allergies }}</p></div>
            @endif
            @if($patient->date_of_birth)
                <div><p class="text-xs text-brand-lavender">Date of Birth</p><p class="font-sans text-brand-cream">{{ $patient->date_of_birth->format('M d, Y') }}</p></div>
            @endif
        </div>
    </div>

    {{-- Tabs-style sections --}}
    <div class="grid lg:grid-cols-2 gap-6">
        {{-- Medical Records --}}
        <div class="bento-card stagger-item p-6">
            <h3 class="font-heading font-semibold text-brand-indigo mb-4 flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-brand-indigo text-white flex items-center justify-center text-sm">📋</span>
                Medical Records
            </h3>
            @forelse($patient->medicalRecords as $record)
                <div class="border-b border-brand-lavender/20 py-4 last:border-0">
                    <div class="flex justify-between items-start">
                        <p class="font-heading font-medium text-brand-indigo">{{ $record->record_type }}</p>
                        <span class="text-xs font-sans text-brand-indigo/50">{{ $record->visit_date?->format('M d, Y') }}</span>
                    </div>
                    <p class="text-sm font-sans text-brand-indigo/70 mt-1"><strong>Diagnosis:</strong> {{ $record->diagnosis ?? '—' }}</p>
                    <p class="text-sm font-sans text-brand-indigo/70"><strong>Treatment:</strong> {{ $record->treatment ?? '—' }}</p>
                    <p class="text-xs font-sans text-brand-indigo/40 mt-1">Dr. {{ $record->doctor?->user?->name }}</p>
                </div>
            @empty
                <p class="font-sans text-brand-indigo/50 text-sm">No medical records yet.</p>
            @endforelse
        </div>

        {{-- Prescriptions --}}
        <div class="bento-card stagger-item p-6">
            <h3 class="font-heading font-semibold text-brand-indigo mb-4">Prescriptions</h3>
            @forelse($patient->prescriptions as $rx)
                <div class="border-b border-brand-lavender/20 py-3 last:border-0">
                    <div class="flex justify-between">
                        <p class="font-heading font-medium text-brand-indigo">{{ $rx->medication }}</p>
                        @if($rx->is_active)
                            <span class="tag-accent text-xs">Active</span>
                        @endif
                    </div>
                    <p class="text-sm font-sans text-brand-indigo/60">{{ $rx->dosage }}</p>
                    <p class="text-xs font-sans text-brand-indigo/40">{{ $rx->prescribed_date?->format('M d, Y') }} · Dr. {{ $rx->doctor?->user?->name }}</p>
                </div>
            @empty
                <p class="font-sans text-brand-indigo/50 text-sm">No prescriptions on file.</p>
            @endforelse
        </div>

        {{-- Lab Results --}}
        <div class="bento-card stagger-item p-6">
            <h3 class="font-heading font-semibold text-brand-indigo mb-4">Lab Results</h3>
            @forelse($patient->labResults as $lab)
                <div class="border-b border-brand-lavender/20 py-3 last:border-0">
                    <div class="flex justify-between">
                        <p class="font-heading font-medium text-brand-indigo">{{ $lab->test_name }}</p>
                        <span class="text-xs font-sans text-brand-indigo/50">{{ $lab->test_date->format('M d, Y') }}</span>
                    </div>
                    <p class="text-sm font-sans text-brand-indigo/70 mt-1">{{ $lab->result }}</p>
                </div>
            @empty
                <p class="font-sans text-brand-indigo/50 text-sm">No lab results yet.</p>
            @endforelse
        </div>

        {{-- Appointment History --}}
        <div class="bento-card stagger-item p-6">
            <h3 class="font-heading font-semibold text-brand-indigo mb-4">Appointment History</h3>
            @forelse($patient->appointments->take(8) as $appt)
                <div class="flex justify-between items-center py-3 border-b border-brand-lavender/20 last:border-0">
                    <div>
                        <p class="font-sans text-sm text-brand-indigo">{{ $appt->reason }}</p>
                        <p class="text-xs text-brand-indigo/50">{{ $appt->scheduled_at->format('M d, Y H:i') }} · Dr. {{ $appt->doctor?->user?->name }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-sans {{ $appt->statusColor() }}">{{ $appt->statusLabel() }}</span>
                </div>
            @empty
                <p class="font-sans text-brand-indigo/50 text-sm">No appointments yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
