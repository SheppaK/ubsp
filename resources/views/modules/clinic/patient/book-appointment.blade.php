@extends('layouts.platform')

@section('title', 'Book Appointment')
@section('header', 'Book an Appointment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <a href="{{ route('modules.clinic.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral hero-animate">&larr; Clinic Home</a>

    <form method="POST" action="{{ route('modules.clinic.appointments.store') }}" class="bento-card p-8 space-y-6 hero-animate">
        @csrf

        <div>
            <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Select Doctor *</label>
            <select name="doctor_id" class="input-field" required>
                <option value="">Choose a doctor...</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" @selected(old('doctor_id', $selectedDoctor?->id) == $doc->id)>
                        {{ $doc->user->name }} — {{ $doc->specialization }}
                    </option>
                @endforeach
            </select>
            @error('doctor_id')<p class="text-sm text-brand-coral mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Preferred Date & Time *</label>
            <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                   min="{{ now()->addHour()->format('Y-m-d\TH:i') }}" class="input-field" required>
            @error('scheduled_at')<p class="text-sm text-brand-coral mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Reason for Visit *</label>
            <input type="text" name="reason" value="{{ old('reason') }}" class="input-field"
                   placeholder="e.g. General check-up, follow-up, flu symptoms" required>
        </div>

        <div>
            <label class="block text-sm font-sans font-medium text-brand-indigo mb-1">Additional Notes</label>
            <textarea name="patient_notes" rows="3" class="input-field" placeholder="Any symptoms or information for the doctor...">{{ old('patient_notes') }}</textarea>
        </div>

        <div class="bento-card-accent p-4 text-sm font-sans">
            <p>Your request will be sent to the healthcare provider for approval. You'll be notified once confirmed.</p>
        </div>

        <button type="submit" class="btn-primary w-full">Submit Appointment Request</button>
    </form>
</div>
@endsection
