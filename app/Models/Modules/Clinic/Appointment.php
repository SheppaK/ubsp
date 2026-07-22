<?php

namespace App\Models\Modules\Clinic;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    protected $table = 'cl_appointments';

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'reason',
        'scheduled_at',
        'status',
        'notes',
        'patient_notes',
        'provider_notes',
        'responded_at',
        'responded_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'responded_at' => 'datetime',
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

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function isAwaitingApproval(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /** @deprecated Use isAwaitingApproval() for pending approval checks */
    public function isPending(): bool
    {
        return $this->isAwaitingApproval();
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_CONFIRMED, self::STATUS_SCHEDULED => 'Confirmed',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-brand-amber/30 text-brand-indigo',
            self::STATUS_CONFIRMED, self::STATUS_SCHEDULED => 'bg-brand-lavender/40 text-brand-indigo',
            self::STATUS_COMPLETED => 'bg-brand-indigo/20 text-brand-indigo',
            self::STATUS_CANCELLED => 'bg-brand-lavender/30 text-brand-indigo/60',
            self::STATUS_REJECTED => 'bg-brand-coral/20 text-brand-coral',
            default => 'tag',
        };
    }
}
