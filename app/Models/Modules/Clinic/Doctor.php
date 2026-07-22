<?php

namespace App\Models\Modules\Clinic;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $table = 'cl_doctors';

    protected $fillable = [
        'user_id',
        'specialization',
        'license_number',
        'bio',
        'accepts_appointments',
    ];

    protected function casts(): array
    {
        return [
            'accepts_appointments' => 'boolean',
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

    public static function forUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'specialization' => 'General Practice',
                'license_number' => 'MD-'.str_pad((string) $user->id, 5, '0', STR_PAD_LEFT),
            ]
        );
    }
}
