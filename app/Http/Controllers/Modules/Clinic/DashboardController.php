<?php

namespace App\Http\Controllers\Modules\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Modules\Clinic\Appointment;
use App\Models\Modules\Clinic\Doctor;
use App\Models\Modules\Clinic\Patient;
use App\Services\ModuleManager;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected ModuleManager $modules) {}

    public function __invoke(): View
    {
        $user = auth()->user();
        $isProvider = $this->isProvider($user);
        $patient = Patient::where('user_id', $user->id)->first();

        $stats = [
            'doctors' => Doctor::where('accepts_appointments', true)->count(),
            'my_appointments' => $patient
                ? Appointment::where('patient_id', $patient->id)->count()
                : 0,
            'upcoming' => $patient
                ? Appointment::where('patient_id', $patient->id)
                    ->whereIn('status', ['pending', 'confirmed', 'scheduled'])
                    ->where('scheduled_at', '>=', now())
                    ->count()
                : 0,
            'pending_requests' => $isProvider
                ? Appointment::where('status', 'pending')
                    ->when(! $user->hasAnyRole(['super-admin', 'administrator']), function ($q) use ($user) {
                        $doctor = Doctor::where('user_id', $user->id)->first();
                        $q->where('doctor_id', $doctor?->id ?? 0);
                    })
                    ->count()
                : 0,
            'patients' => $isProvider ? Patient::count() : 0,
        ];

        return view('modules.clinic.dashboard', [
            'config' => $this->modules->get('clinic'),
            'stats' => $stats,
            'isProvider' => $isProvider,
            'patient' => $patient,
            'doctors' => Doctor::with('user')->where('accepts_appointments', true)->take(4)->get(),
        ]);
    }

    private function isProvider($user): bool
    {
        return $user->hasAnyRole(['super-admin', 'administrator', 'doctor', 'manager', 'staff'])
            || $user->can('clinic.manage-patients')
            || $user->can('clinic.manage-appointments');
    }
}
