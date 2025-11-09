<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayersCard extends Model
{
    protected $table = 'players_cards';

    protected $fillable = [
        'player_id',
        'type_id',
        'energy',
        'week_id',
        'stats',
    ];

    protected $casts = [
        'stats' => 'array',
    ];

    // 🔗 Relationships
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function type()
    {
        return $this->belongsTo(CardType::class, 'type_id');
    }
}
