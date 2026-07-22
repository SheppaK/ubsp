<?php

namespace App\Models\Modules\ExchangeTracker;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'ex_rates';

    protected $fillable = [
        'currency_code',
        'rate',
        'recorded_date',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:6',
            'recorded_date' => 'date',
        ];
    }
}
