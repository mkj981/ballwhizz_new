<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    use HasFactory;

    protected $table = 'teams'; // ✅ Explicitly define (good practice for clarity)

    protected $fillable = [
        'is_top_team',
        'country_id',
        'venue_id',
        'gender',
        'en_name',
        'ar_name',
        'short_code',
        'image_path',
        'founded',
        'type',
        'placeholder',
        'status',
    ];

    protected $casts = [
        'is_top_team' => 'boolean',
        'placeholder' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * 🔗 Relationship: Team belongs to a Country
     */
    public function country()
    {
        return $this->belongsTo(Countries::class, 'country_id')
            ->withDefault([
                'en_name' => 'Unknown Country',
            ]);
    }

    /**
     * 🔗 Relationship: Team belongs to a Venue
     */
    public function venue()
    {
        return $this->belongsTo(Venues::class, 'venue_id')
            ->withDefault([
                'name' => 'Unknown Venue',
            ]);
    }

    public function seasons()
    {
        return $this->belongsToMany(Seasons::class, 'season_team', 'team_id', 'season_id')
            ->withTimestamps();
    }

    public function homeMatches()
    {
        return $this->hasMany(PredictionCardsMatches::class, 'home_team_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(PredictionCardsMatches::class, 'away_team_id');
    }

}
