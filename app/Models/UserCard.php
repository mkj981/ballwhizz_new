<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_id',
        'league_id',
        'position_id',
        'is_in_team',
        'is_sub',
        'in_stad',
    ];

    /**
     * 🔗 Relationships
     */

    // 🧍 The user who owns this card
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 🃏 The players_cards entry (the card itself)
    public function card()
    {
        return $this->belongsTo(PlayersCard::class, 'card_id');
    }

    // 🏆 The league the card belongs to
    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    // 📍 The position of the player
    public function position()
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }

    // ⚽ Direct access to the Player through the PlayersCard relation
    public function player()
    {
        return $this->hasOneThrough(
            Players::class,          // Final model
            PlayersCard::class,      // Intermediate model
            'id',                    // Foreign key on PlayersCard (local)
            'id',                    // Foreign key on Players (local)
            'card_id',               // Local key on UserCard
            'player_id'              // Local key on PlayersCard
        );
    }
}
