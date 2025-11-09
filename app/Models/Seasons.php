<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seasons extends Model
{
    use HasFactory;

    protected $table = 'seasons';

    protected $fillable = [
        'league_id',
        'tie_breaker_rule_id',
        'name',
        'finished',
        'pending',
        'is_current',
        'starting_at',
        'ending_at',
        'standings_recalculated_at',
        'status',
    ];

    // 🔗 Relationships
    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }
    public function teams()
    {
        return $this->belongsToMany(\App\Models\Teams::class, 'season_team', 'season_id', 'team_id')
            ->with('country')
            ->withTimestamps();
    }

}
