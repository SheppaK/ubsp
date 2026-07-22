<?php

namespace App\Models\Modules\ElectronicsTracker;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    protected $table = 'et_assets';

    protected $fillable = [
        'name',
        'type',
        'serial_number',
        'qr_code',
        'purchase_date',
        'warranty_expires',
        'assigned_to',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expires' => 'date',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
