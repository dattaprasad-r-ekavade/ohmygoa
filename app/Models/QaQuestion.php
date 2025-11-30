<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QaQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'content', 'tags',
        'views_count', 'answers_count', 'votes_count',
        'accepted_answer_id', 'is_answered', 'status',
        'closed_at', 'closed_reason'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_answered' => 'boolean',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function answers()
    {
        return $this->hasMany(QaAnswer::class, 'question_id');
    }

    public function acceptedAnswer()
    {
        return $this->belongsTo(QaAnswer::class, 'accepted_answer_id');
    }

    public function votes()
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAnswered($query)
    {
        return $query->where('is_answered', true);
    }

    public function scopeUnanswered($query)
    {
        return $query->where('is_answered', false);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('votes_count', 'desc');
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function acceptAnswer(QaAnswer $answer)
    {
        $this->update([
            'accepted_answer_id' => $answer->id,
            'is_answered' => true
        ]);
        $answer->update(['is_accepted' => true]);
    }

    public function close($reason = null)
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closed_reason' => $reason
        ]);
    }

    // Static methods
    public static function getStatuses()
    {
        return ['active', 'closed', 'deleted'];
    }
}
