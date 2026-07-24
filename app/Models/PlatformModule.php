<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformModule extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'color',
        'is_enabled',
        'sort_order',
        'price_zmw',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'price_zmw' => 'decimal:2',
        ];
    }
}
