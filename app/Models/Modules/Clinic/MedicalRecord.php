<?php

namespace App\Models\Modules\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $table = 'cl_medical_records';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        'record_type',
        'diagnosis',
        'treatment',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
