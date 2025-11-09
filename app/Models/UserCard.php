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

    // 🔗 Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(PlayersCard::class, 'card_id');
    }

    public function league()
    {
        return $this->belongsTo(Leagues::class, 'league_id');
    }

    public function position()
    {
        return $this->belongsTo(Positions::class, 'position_id');
    }
}
