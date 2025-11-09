<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_id', 'user_id', 'comment', 'rating'
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Automatically update news rating when a comment is added or removed
    protected static function booted()
    {
        static::saved(function ($comment) {
            $comment->news?->updateAverageRating();
        });

        static::deleted(function ($comment) {
            $comment->news?->updateAverageRating();
        });
    }
}
