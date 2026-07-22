<?php

namespace App\Models\Modules\SportsLeague;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $table = 'sl_leagues';

    protected $fillable = [
        'name',
        'season',
    ];
}
