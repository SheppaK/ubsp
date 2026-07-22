<?php

namespace App\Http\Controllers\Modules\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Modules\Clinic\Appointment;
use App\Models\Modules\Clinic\Doctor;
use App\Models\Modules\Clinic\LabResult;
use App\Models\Modules\Clinic\MedicalRecord;
use App\Models\Modules\Clinic\Patient;
use App\Models\Modules\Clinic\Prescription;
use App\Services\ModuleManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function appointments(Request $request): View
    {
        $user = auth()->user();
        $query = Appointment::with(['patient', 'doctor.user'])->latest('scheduled_at');

        if (! $user->hasAnyRole(['super-admin', 'administrator'])) {
            $doctor = Doctor::where('user_id', $user->id)->first();
            $query->where('doctor_id', $doctor?->id ?? 0);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return view('modules.clinic.provider.appointments', [
            'config' => $this->modules->get('clinic'),
            'appointments' => $query->paginate(15)->withQueryString(),
            'statusFilter' => $request->input('status'),
        ]);
    }

    public function confirm(Appointment $appointment): RedirectResponse
    {
        $this->authorizeAppointment($appointment);
        abort_unless($appointment->status === Appointment::STATUS_PENDING, 422);

        $appointment->update([
            'status' => Appointment::STATUS_CONFIRMED,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Appointment confirmed.');
    }

    public function reject(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeAppointment($appointment);
        abort_unless($appointment->status === Appointment::STATUS_PENDING, 422);

        $request->validate(['provider_notes' => ['nullable', 'string', 'max:500']]);

        $appointment->update([
            'status' => Appointment::STATUS_REJECTED,
            'provider_notes' => $request->provider_notes,
            'responded_at' => now(),
            'responded_by' => auth()->id(),
        ]);

        return back()->with('success', 'Appointment request declined.');
    }

    public function complete(Appointment $appointment): RedirectResponse
    {
        $this->authorizeAppointment($appointment);
        abort_unless(in_array($appointment->status, ['confirmed', 'scheduled']), 422);

        $appointment->update(['status' => Appointment::STATUS_COMPLETED]);

        return back()->with('success', 'Appointment marked as completed.');
    }

    public function patients(): View
    {
        return view('modules.clinic.provider.patients.index', [
            'config' => $this->modules->get('clinic'),
            'patients' => Patient::withCount('appointments')->latest()->paginate(15),
        ]);
    }

    public function showPatient(Patient $patient): View
    {
        $patient->load([
            'medicalRecords.doctor.user',
            'labResults',
            'prescriptions.doctor.user',
            'appointments.doctor.user',
            'bills',
        ]);

        return view('modules.clinic.provider.patients.show', [
            'config' => $this->modules->get('clinic'),
            'patient' => $patient,
        ]);
    }

    public function storeMedicalRecord(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'visit_date' => ['required', 'date'],
            'record_type' => ['required', 'string', 'max:100'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
        ]);

        $doctor = Doctor::forUser(auth()->user());

        $patient->medicalRecords()->create([
            ...$validated,
            'doctor_id' => $doctor->id,
        ]);

        return back()->with('success', 'Medical record added.');
    }

    public function storePrescription(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'medication' => ['required', 'string'],
            'dosage' => ['nullable', 'string'],
            'prescribed_date' => ['required', 'date'],
        ]);

        $doctor = Doctor::forUser(auth()->user());

        $patient->prescriptions()->create([
            ...$validated,
            'doctor_id' => $doctor->id,
            'is_active' => true,
        ]);

        return back()->with('success', 'Prescription added.');
    }

    public function storeLabResult(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'test_name' => ['required', 'string', 'max:255'],
            'result' => ['required', 'string'],
            'test_date' => ['required', 'date'],
        ]);

        $patient->labResults()->create($validated);

        return back()->with('success', 'Lab result added.');
    }

    private function authorizeAppointment(Appointment $appointment): void
    {
        $user = auth()->user();
        if ($user->hasAnyRole(['super-admin', 'administrator'])) {
            return;
        }

        $doctor = Doctor::where('user_id', $user->id)->first();
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);
    }
}
