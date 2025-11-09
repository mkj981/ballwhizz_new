<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiType extends Model
{
    use HasFactory;

    protected $fillable = [
        'en_name',
        'ar_name',
        'developer_name',
        'model_type',
    ];
}
