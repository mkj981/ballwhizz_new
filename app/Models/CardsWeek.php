<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardsWeek extends Model
{
    use HasFactory;

    protected $table = 'cards_weeks';

    protected $fillable = [
        'week_months_id',
        'league_id',
        'matchday',
        'start',
        'end',
        'close_at',
        'is_active',
        'is_open',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'close_at' => 'datetime',
        'is_active' => 'boolean',
        'is_open' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * 🔹 Linked Week/Month
     */
    public function weekMonth()
    {
        return $this->belongsTo(WeekMonth::class, 'week_months_id');
    }

    /**
     * 🔹 Linked League
     */
    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    /**
     * 🔹 Optional Relation: Matches (if linked later)
     */
    public function matches()
    {
        return $this->hasMany(PredictionCardsMatches::class, 'league_id', 'league_id');
    }
}
