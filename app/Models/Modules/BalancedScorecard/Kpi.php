<?php

namespace App\Models\Modules\BalancedScorecard;

use Illuminate\Database\Eloquent\Model;

class Kpi extends Model
{
    protected $table = 'bsc_kpis';

    protected $fillable = [
        'objective_id',
        'name',
        'unit',
        'target',
        'actual',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target' => 'decimal:2',
            'actual' => 'decimal:2',
        ];
    }
}
