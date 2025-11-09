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

    // 🔹 Relationships
    public function match()
    {
        return $this->belongsTo(PredictionCardsMatches::class, 'prediction_match_id');
    }

    public function player()
    {
        return $this->belongsTo(Players::class, 'player_id');
    }
}
