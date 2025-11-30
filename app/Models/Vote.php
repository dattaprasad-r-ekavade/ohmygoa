<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'voteable_id', 'voteable_type', 'vote_type'
    ];

    protected $casts = [
        'vote_type' => 'integer'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function voteable()
    {
        return $this->morphTo();
    }

    // Static methods
    public static function upvote($user, $voteable)
    {
        return static::updateOrCreate(
            [
                'user_id' => $user->id,
                'voteable_id' => $voteable->id,
                'voteable_type' => get_class($voteable)
            ],
            ['vote_type' => 1]
        );
    }

    public static function downvote($user, $voteable)
    {
        return static::updateOrCreate(
            [
                'user_id' => $user->id,
                'voteable_id' => $voteable->id,
                'voteable_type' => get_class($voteable)
            ],
            ['vote_type' => -1]
        );
    }

    public static function removeVote($user, $voteable)
    {
        return static::where([
            'user_id' => $user->id,
            'voteable_id' => $voteable->id,
            'voteable_type' => get_class($voteable)
        ])->delete();
    }
}
