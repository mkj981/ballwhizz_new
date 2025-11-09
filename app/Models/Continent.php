<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Continent extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'en_name',
        'ar_name',
        'dark_img',
        'light_img',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function countries()
    {
        return $this->hasMany(Countries::class, 'continent_id');
    }
}
