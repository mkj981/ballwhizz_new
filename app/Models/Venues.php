<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venues extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'city_id',
        'name',
        'address',
        'zipcode',
        'latitude',
        'longitude',
        'capacity',
        'image_path',
        'city_name',
        'surface',
        'national_team',
        'status',
    ];

    // 🔗 Relationships
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }

    public function teams()
    {
        return $this->hasMany(Teams::class, 'venue_id');
    }
}
