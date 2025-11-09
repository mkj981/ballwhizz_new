<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id',
        'season_id',
        'country_id',
        'league_id',
        'team_id',
        'position_id',
        'name',
        'en_common_name',
        'ar_common_name',
        'date_of_birth',
        'image_path',
        'default_image',
        'open_image',
        'display_name',
    ];

    // 🔗 Relationships
    public function season()
    {
        return $this->belongsTo(Seasons::class, 'season_id');
    }

    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    public function team()
    {
        return $this->belongsTo(Teams::class, 'team_id');
    }

    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id');
    }



    public function matchScorers()
    {
        return $this->hasMany(PredictionCardsMatchScorer::class, 'player_id');
    }

}
