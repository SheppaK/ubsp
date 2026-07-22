@extends('layouts.platform')

@section('title', 'Medical Profile')
@section('header', 'My Medical Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <a href="{{ route('modules.clinic.dashboard') }}" class="font-sans text-sm text-brand-indigo/70 hover:text-brand-coral hero-animate">&larr; Clinic Home</a>

    <form method="POST" action="{{ route('modules.clinic.profile.update') }}" class="bento-card mt-4 p-8 space-y-4 hero-animate">
        @csrf @method('PUT')
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-sans font-medium mb-1">First Name *</label>
                <input name="first_name" value="{{ old('first_name', $patient->first_name) }}" class="input-field" required>
            </div>
            <div>
                <label class="block text-sm font-sans font-medium mb-1">Last Name *</label>
                <input name="last_name" value="{{ old('last_name', $patient->last_name) }}" class="input-field" required>
            </div>
            <div>
                <label class="block text-sm font-sans font-medium mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-sans font-medium mb-1">Phone</label>
                <input name="phone" value="{{ old('phone', $patient->phone) }}" class="input-field">
            </div>
            <div>
                <label class="block text-sm font-sans font-medium mb-1">Blood Type</label>
                <select name="blood_type" class="input-field">
                    <option value="">Select...</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
                        <option value="{{ $bt }}" @selected(old('blood_type', $patient->blood_type) === $bt)>{{ $bt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-sans font-medium mb-1">Address</label>
                <textarea name="address" rows="2" class="input-field">{{ old('address', $patient->address) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-sans font-medium mb-1">Allergies</label>
                <textarea name="allergies" rows="2" class="input-field" placeholder="List any known allergies...">{{ old('allergies', $patient->allergies) }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-sans font-medium mb-1">Emergency Contact</label>
                <input name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" class="input-field" placeholder="Name — Phone number">
            </div>
        </div>
        <button type="submit" class="btn-primary">Save Profile</button>
    </form>
</div>
@endsection
