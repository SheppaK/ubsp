@extends('layouts.platform')

@section('title', $config['name'])
@section('header', $config['name'])

@section('content')
<div class="space-y-8">
    <div class="bento-card-dark hero-animate p-8 lg:p-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <h2 class="font-heading text-2xl lg:text-3xl font-bold text-brand-cream">Clinic Management</h2>
                <p class="font-sans text-brand-lavender mt-2 max-w-xl">Book appointments, access your health history, and connect with healthcare providers.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('modules.clinic.appointments.create') }}" class="btn-primary">Book Appointment</a>
                <a href="{{ route('modules.clinic.health-history') }}" class="btn-accent">Health History</a>
                @if($isProvider)
                    <a href="{{ route('modules.clinic.provider.appointments') }}" class="btn-ghost !text-brand-cream !border-brand-lavender/50">Provider Inbox</a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60">Doctors Available</p>
            <p class="text-3xl font-heading font-bold text-brand-indigo" data-count="{{ $stats['doctors'] }}">{{ $stats['doctors'] }}</p>
        </div>
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60">My Appointments</p>
            <p class="text-3xl font-heading font-bold text-brand-coral">{{ $stats['my_appointments'] }}</p>
        </div>
        <div class="stat-card stagger-item">
            <p class="text-sm font-sans text-brand-indigo/60">Upcoming</p>
            <p class="text-3xl font-heading font-bold text-brand-indigo">{{ $stats['upcoming'] }}</p>
        </div>
        @if($isProvider)
            <div class="stat-card stagger-item bento-card-accent">
                <p class="text-sm font-sans opacity-80">Pending Requests</p>
                <p class="text-3xl font-heading font-bold">{{ $stats['pending_requests'] }}</p>
                @if($stats['pending_requests'] > 0)
                    <a href="{{ route('modules.clinic.provider.appointments', ['status' => 'pending']) }}" class="text-sm font-sans underline mt-1">Review</a>
                @endif
            </div>
        @else
            <div class="stat-card stagger-item">
                <a href="{{ route('modules.clinic.profile') }}" class="font-sans text-sm text-brand-coral hover:underline">Update medical profile →</a>
            </div>
        @endif
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div>
            <h3 class="font-heading text-xl font-semibold text-brand-indigo mb-4 stagger-item">Our Doctors</h3>
            <div class="space-y-3">
                @foreach($doctors as $doc)
                    <div class="bento-card stagger-item flex items-center justify-between p-4" data-hover-lift>
                        <div>
                            <p class="font-heading font-semibold text-brand-indigo">{{ $doc->user->name }}</p>
                            <p class="font-sans text-sm text-brand-indigo/60">{{ $doc->specialization }}</p>
                        </div>
                        <a href="{{ route('modules.clinic.appointments.create', ['doctor_id' => $doc->id]) }}" class="btn-secondary text-xs py-2 px-4">Book</a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-4">
            <div class="bento-card stagger-item p-6">
                <h3 class="font-heading font-semibold text-brand-indigo mb-3">Patient Quick Links</h3>
                <ul class="space-y-2 font-sans text-sm">
                    <li><a href="{{ route('modules.clinic.health-history') }}" class="text-brand-coral hover:underline">View health history & records</a></li>
                    <li><a href="{{ route('modules.clinic.appointments.mine') }}" class="text-brand-coral hover:underline">My appointment requests</a></li>
                    <li><a href="{{ route('modules.clinic.profile') }}" class="text-brand-coral hover:underline">Medical profile & allergies</a></li>
                </ul>
            </div>
            @if($isProvider)
                <div class="bento-card-dark stagger-item p-6">
                    <h3 class="font-heading font-semibold text-brand-cream mb-3">Provider Tools</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('modules.clinic.provider.appointments') }}" class="btn-accent text-sm">Appointment Inbox</a>
                        <a href="{{ route('modules.clinic.provider.patients.index') }}" class="btn-ghost !text-brand-cream text-sm py-2">All Patients</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
