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

class AppointmentController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function create(Request $request): View
    {
        $doctors = Doctor::with('user')
            ->where('accepts_appointments', true)
            ->get();

        return view('modules.clinic.patient.book-appointment', [
            'config' => $this->modules->get('clinic'),
            'doctors' => $doctors,
            'selectedDoctor' => $request->doctor_id ? Doctor::find($request->doctor_id) : null,
            'patient' => Patient::forUser(auth()->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $patient = Patient::forUser(auth()->user());

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:cl_doctors,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'reason' => ['required', 'string', 'max:255'],
            'patient_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $doctor = Doctor::findOrFail($validated['doctor_id']);
        abort_unless($doctor->accepts_appointments, 422);

        $duplicate = Appointment::where('patient_id', $patient->id)
            ->where('doctor_id', $doctor->id)
            ->where('status', 'pending')
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['doctor_id' => 'You already have a pending request with this doctor.']);
        }

        Appointment::create([
            ...$validated,
            'patient_id' => $patient->id,
            'status' => Appointment::STATUS_PENDING,
        ]);

        return redirect()
            ->route('modules.clinic.appointments.mine')
            ->with('success', 'Appointment request submitted. A healthcare provider will review it shortly.');
    }

    public function mine(): View
    {
        $patient = Patient::forUser(auth()->user());

        $appointments = Appointment::with(['doctor.user'])
            ->where('patient_id', $patient->id)
            ->latest('scheduled_at')
            ->paginate(10);

        return view('modules.clinic.patient.my-appointments', [
            'config' => $this->modules->get('clinic'),
            'appointments' => $appointments,
        ]);
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $patient = Patient::forUser(auth()->user());
        abort_unless($appointment->patient_id === $patient->id, 403);
        abort_unless(in_array($appointment->status, ['pending', 'confirmed', 'scheduled']), 422);

        $appointment->update(['status' => Appointment::STATUS_CANCELLED]);

        return back()->with('success', 'Appointment cancelled.');
    }
}
