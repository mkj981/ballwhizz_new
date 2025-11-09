<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PredictionCardsMatches extends Model
{
    use HasFactory;

    protected $table = 'prediction_cards_matches';

    protected $fillable = [
        'league_id',
        'match_id',
        'home_team_id',
        'away_team_id',
        'starting_at',
        'home_team_result',
        'away_team_result',
        'status',
    ];

    protected $casts = [
        'home_team_result' => 'integer',
        'away_team_result' => 'integer',
        'status' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /** 🔹 League relation */
    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    /** 🔹 Home Team relation */
    public function homeTeam()
    {
        return $this->belongsTo(Teams::class, 'home_team_id');
    }

    /** 🔹 Away Team relation */
    public function awayTeam()
    {
        return $this->belongsTo(Teams::class, 'away_team_id');
    }

    /** 🔹 Scorers relation */
    public function scorers()
    {
        return $this->hasMany(PredictionCardsMatchScorer::class, 'match_id', 'id');
    }
}
