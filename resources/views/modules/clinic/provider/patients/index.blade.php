@extends('layouts.platform')

@section('title', 'Patients')
@section('header', 'Patient Management')

@section('content')
<div class="space-y-6">
    <div class="hero-animate flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('modules.clinic.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral">&larr; Clinic Home</a>
        <a href="{{ route('modules.clinic.provider.appointments') }}" class="btn-secondary text-sm py-2">Appointment Inbox</a>
    </div>

    <div class="bento-card stagger-item overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-brand-lavender/30">
                        <th class="px-6 py-4 font-heading text-sm font-semibold text-brand-indigo">Patient</th>
                        <th class="px-6 py-4 font-heading text-sm font-semibold text-brand-indigo hidden md:table-cell">Contact</th>
                        <th class="px-6 py-4 font-heading text-sm font-semibold text-brand-indigo hidden lg:table-cell">Blood Type</th>
                        <th class="px-6 py-4 font-heading text-sm font-semibold text-brand-indigo">Appointments</th>
                        <th class="px-6 py-4 font-heading text-sm font-semibold text-brand-indigo"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr class="border-b border-brand-lavender/20 last:border-0 hover:bg-brand-lavender/10 transition">
                            <td class="px-6 py-4">
                                <p class="font-heading font-medium text-brand-indigo">{{ $patient->fullName() }}</p>
                                @if($patient->allergies)
                                    <p class="text-xs font-sans text-brand-coral mt-0.5">⚠ {{ Str::limit($patient->allergies, 40) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <p class="font-sans text-sm text-brand-indigo/70">{{ $patient->email ?? '—' }}</p>
                                <p class="font-sans text-xs text-brand-indigo/50">{{ $patient->phone ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 hidden lg:table-cell font-sans text-sm text-brand-indigo/70">
                                {{ $patient->blood_type ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="tag text-xs">{{ $patient->appointments_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('modules.clinic.provider.patients.show', $patient) }}" class="btn-ghost text-xs py-2">View Records</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <p class="font-heading text-xl text-brand-indigo">No patients yet</p>
                                <p class="font-sans text-brand-indigo/60 mt-2">Patients appear when they register or book appointments.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $patients->links() }}
</div>
@endsection
