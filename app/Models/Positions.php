<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Positions extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'code',
        'en_name',
        'ar_name',
    ];

    public $incrementing = false; // ✅ because we reuse IDs from api_types
}
