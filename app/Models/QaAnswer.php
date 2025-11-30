<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QaAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question_id', 'user_id', 'content',
        'votes_count', 'is_accepted'
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function question()
    {
        return $this->belongsTo(QaQuestion::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    // Scopes
    public function scopeAccepted($query)
    {
        return $query->where('is_accepted', true);
    }

    public function scopeByQuestion($query, $questionId)
    {
        return $query->where('question_id', $questionId);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('votes_count', 'desc');
    }
}
