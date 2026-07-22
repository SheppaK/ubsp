<?php

namespace App\Models\Modules\Clinic;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $table = 'cl_patients';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'phone',
        'email',
        'address',
        'blood_type',
        'allergies',
        'emergency_contact',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class)->latest('visit_date');
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class)->latest('test_date');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->latest('prescribed_date');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class)->latest();
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public static function forUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => explode(' ', $user->name)[0] ?? $user->name,
                'last_name' => explode(' ', $user->name, 2)[1] ?? '',
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        );
    }
}
