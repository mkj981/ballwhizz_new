<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OddsBookmaker extends Model
{
    protected $table = 'odds_bookmakers';

    protected $fillable = [
        'legacy_id',
        'name',
    ];
}
