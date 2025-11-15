<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OddsMarket extends Model
{
    protected $table = 'odds_markets';

    protected $fillable = [
        'legacy_id',
        'name',
        'developer_name',
        'has_winning_calculations',
    ];
}
