<?php

namespace App\Http\Controllers\Modules\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Modules\Clinic\Appointment;
use App\Models\Modules\Clinic\Doctor;
use App\Models\Modules\Clinic\Patient;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientPortalController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function healthHistory(): View
    {
        $patient = Patient::forUser(auth()->user());
        $patient->load([
            'medicalRecords.doctor.user',
            'labResults',
            'prescriptions.doctor.user',
            'bills',
            'appointments.doctor.user',
        ]);

        return view('modules.clinic.patient.health-history', [
            'config' => $this->modules->get('clinic'),
            'patient' => $patient,
        ]);
    }

    public function profile(): View
    {
        $patient = Patient::forUser(auth()->user());

        return view('modules.clinic.patient.profile', [
            'config' => $this->modules->get('clinic'),
            'patient' => $patient,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $patient = Patient::forUser(auth()->user());

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'allergies' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return back()->with('success', 'Medical profile updated.');
    }
}
