<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'league_id',
        'type',
        'player_id',
        'team_id',

        // NEW FIELDS
        'home_team_id',
        'away_team_id',
        'home_prediction',
        'away_prediction',

        'card_id',
        'points',
        'scorer_list',
        'home_team_result',
        'away_team_result',
        'cards_week_id',
        'prediction_week_id',
        'is_sub',
        'position',
        'game_date',
        'game_user_date',
    ];

    /** 🔗 Relationships */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    public function team()
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }

    public function card()
    {
        return $this->belongsTo(PlayersCard::class, 'card_id');
    }

    public function cardsWeek()
    {
        return $this->belongsTo(CardsWeek::class, 'cards_week_id');
    }

    public function predictionWeek()
    {
        return $this->belongsTo(WeekMonth::class, 'prediction_week_id');
    }

    // NEW: relationships for home/away teams
    public function homeTeam()
    {
        return $this->belongsTo(Teams::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Teams::class, 'away_team_id');
    }
}
