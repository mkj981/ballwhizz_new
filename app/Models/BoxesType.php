<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxesType extends Model
{
    protected $table = 'boxes_types';

    protected $fillable = [
        'en_name',
        'ar_name',
        'en_description',
        'ar_description',
        'time',
        'gold_players',
        'silver_players',
        'bronze_players',
        'special_players',
        'gem',
        'coins',
        'xp',
        'price',
        'swap',
        'swap_power',
        'gem_cost',
        'image',
        'open_image',
        'en_swap_trade_in_desc',
        'ar_swap_trade_in_desc',
        'en_swap_buy_desc',
        'ar_swap_buy_desc',
    ];
}
