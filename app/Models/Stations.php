<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stations extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'image_path',
        'type',
        'related_id',
        'status',
    ];

    // Related station (optional)
    public function related()
    {
        return $this->belongsTo(Stations::class, 'related_id');
    }
}
