<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppNews extends Model
{
    use HasFactory;

    protected $table = 'appnews';

    protected $fillable = [
        'short_text_en',
        'short_text_ar',
        'long_text_en',
        'long_text_ar',
        'video_url',
        'images'
    ];

    protected $casts = [
        'images' => 'array', // JSON → array
    ];
}
