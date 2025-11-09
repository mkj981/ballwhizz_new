<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'en_title', 'ar_title', 'en_text', 'ar_text', 'image',
        'en_short_desc', 'ar_short_desc', 'hashtags', 'video',
        'en_meta_description', 'en_meta_title',
        'ar_meta_description', 'ar_meta_title', 'average_rating'
    ];

    // 🔹 Relationships
    public function comments()
    {
        return $this->hasMany(NewsComment::class);
    }

    public function players()
    {
        return $this->belongsToMany(
            \App\Models\Player::class,
            'news_players',
            'news_id',
            'player_id'
        );
    }

    public function leagues()
    {
        return $this->belongsToMany(
            \App\Models\Leagues::class,
            'news_leagues',
            'news_id',
            'league_id'
        );
    }

    public function teams()
    {
        // 👇 explicitly specify the foreign and related keys
        return $this->belongsToMany(
            \App\Models\Teams::class,
            'news_teams',
            'news_id',   // foreign key on pivot
            'team_id'    // related key on pivot
        );
    }

    // 🔹 Update average rating
    public function updateAverageRating()
    {
        $this->average_rating = $this->comments()->avg('rating') ?? 0;
        $this->save();
    }
}
