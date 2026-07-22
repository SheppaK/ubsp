<?php

namespace Database\Seeders;

use App\Models\Modules\Clinic\Appointment;
use App\Models\Modules\Clinic\Doctor;
use App\Models\Modules\Clinic\LabResult;
use App\Models\Modules\Clinic\MedicalRecord;
use App\Models\Modules\Clinic\Patient;
use App\Models\Modules\Clinic\Prescription;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $doctorUser = User::where('email', 'doctor@ubsp.local')->first()
            ?? User::where('email', 'admin@ubsp.local')->first();
        $patientUser = User::where('email', 'patient@ubsp.local')->first();

        if (! $doctorUser || ! $patientUser) {
            return;
        }

        $doctor = Doctor::forUser($doctorUser);
        $doctor->update([
            'specialization' => 'General Medicine',
            'license_number' => 'MD-2024-001',
            'bio' => '15 years experience in general practice and student health services.',
            'accepts_appointments' => true,
        ]);

        Doctor::forUser(User::where('email', 'admin@ubsp.local')->first())->update([
            'specialization' => 'Internal Medicine',
            'license_number' => 'MD-2024-002',
            'bio' => 'Specialist in internal medicine and chronic disease management.',
            'accepts_appointments' => true,
        ]);

        $patient = Patient::forUser($patientUser);
        $patient->update([
            'first_name' => 'John',
            'last_name' => 'Patient',
            'date_of_birth' => '1998-05-15',
            'phone' => '+263773333444',
            'address' => '14 Campus Road, Harare',
            'blood_type' => 'O+',
            'allergies' => 'Penicillin',
            'emergency_contact' => 'Mary Patient — +263775555666',
        ]);

        MedicalRecord::updateOrCreate(
            ['patient_id' => $patient->id, 'visit_date' => '2025-11-10'],
            [
                'doctor_id' => $doctor->id,
                'record_type' => 'Consultation',
                'diagnosis' => 'Seasonal allergic rhinitis',
                'treatment' => 'Antihistamine prescribed, avoid known allergens',
            ]
        );

        MedicalRecord::updateOrCreate(
            ['patient_id' => $patient->id, 'visit_date' => '2026-01-22'],
            [
                'doctor_id' => $doctor->id,
                'record_type' => 'Follow-up',
                'diagnosis' => 'Resolved — no active symptoms',
                'treatment' => 'Continue preventive measures',
            ]
        );

        LabResult::updateOrCreate(
            ['patient_id' => $patient->id, 'test_name' => 'Full Blood Count', 'test_date' => '2026-01-20'],
            ['result' => 'All values within normal range. Hemoglobin: 14.2 g/dL']
        );

        Prescription::updateOrCreate(
            ['patient_id' => $patient->id, 'medication' => 'Cetirizine 10mg', 'prescribed_date' => '2025-11-10'],
            [
                'doctor_id' => $doctor->id,
                'dosage' => 'Once daily at night',
                'is_active' => false,
            ]
        );

        Appointment::updateOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_at' => now()->addDays(3)->setTime(10, 0),
            ],
            [
                'reason' => 'General check-up',
                'status' => 'confirmed',
                'patient_notes' => 'Would like a routine health assessment',
                'responded_at' => now()->subDay(),
                'responded_by' => $doctorUser->id,
            ]
        );

        Appointment::updateOrCreate(
            [
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'scheduled_at' => now()->addDays(7)->setTime(14, 30),
            ],
            [
                'reason' => 'Follow-up on allergies',
                'status' => 'pending',
                'patient_notes' => 'Mild symptoms returned',
            ]
        );
    }
}
