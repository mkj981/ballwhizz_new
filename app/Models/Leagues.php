<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leagues extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'en_name',
        'ar_name',
        'type',
        'short_code',
        'sub_type',
        'category',
        'image_path',
        'status',
        'cards_status',
    ];

    // 🔗 Relationship: League belongs to a Country
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    // 🔗 Relationship: League has many Prediction Card Matches
    public function predictionMatches()
    {
        return $this->hasMany(PredictionCardsMatches::class, 'league_id');
    }

    /**
     * 🔹 Scope: Only leagues where Cardz feature is active
     *
     * Usage: Leagues::activeCards()->get();
     */
    public function scopeActiveCards($query)
    {
        return $query->where('cards_status', 1);
    }
}
