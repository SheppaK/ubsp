<?php

namespace App\Models\Modules\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prescription extends Model
{
    protected $table = 'cl_prescriptions';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'prescribed_date',
        'medication',
        'dosage',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'prescribed_date' => 'date',
            'is_active' => 'boolean',
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
