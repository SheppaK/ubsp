<?php

namespace App\Models\Modules\Weather;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'wx_locations';

    protected $fillable = [
        'city',
        'country',
        'latitude',
        'longitude',
        'forecast_cache',
        'cached_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'forecast_cache' => 'array',
            'cached_at' => 'datetime',
        ];
    }
}
