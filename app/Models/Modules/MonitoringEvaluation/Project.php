<?php

namespace App\Models\Modules\MonitoringEvaluation;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'me_projects';

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'budget',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'progress' => 'integer',
        ];
    }
}
