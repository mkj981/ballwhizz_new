<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionCardsMatchScorer extends Model
{
    use HasFactory;

    protected $table = 'prediction_cards_match_scorers';

    protected $fillable = [
        'prediction_match_id',
        'player_id',
        'team_side',
        'minute',
        'type',
    ];

    protected $casts = [
        'minute' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /** 🔹 Match Relation */
    public function match()
    {
        return $this->belongsTo(PredictionCardsMatches::class, 'prediction_match_id');
    }

    /** 🔹 Player Relation */
    public function player()
    {
        // ✅ Must point to Player::class, not Players::class
        return $this->belongsTo(Player::class, 'player_id');
    }
}
