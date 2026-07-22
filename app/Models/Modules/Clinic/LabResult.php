<?php

namespace App\Models\Modules\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    protected $table = 'cl_lab_results';

    protected $fillable = [
        'patient_id',
        'test_name',
        'result',
        'test_date',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
