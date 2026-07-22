<?php

namespace App\Models\Modules\SubscriptionSharing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Plan extends Model
{
    protected $table = 'ss_plans';

    protected $fillable = [
        'name',
        'provider',
        'monthly_cost',
        'max_members',
        'renewal_date',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'monthly_cost' => 'decimal:2',
            'max_members' => 'integer',
            'renewal_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
