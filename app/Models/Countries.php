<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $fillable = [
        'en_name',
        'ar_name',
        'continent_id',
        'fifa_name',
        'iso2',
        'iso3',
        'latitude',
        'longitude',
        'borders',
        'image_path',
        'status',
    ];

    // 🌍 Relation: Each country belongs to a continent
    public function continent()
    {
        return $this->belongsTo(Continent::class, 'continent_id');
    }
}
