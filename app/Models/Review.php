<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'title',
        'comment',
        'images',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
        'not_helpful_count',
        'reply_by',
        'reply_text',
        'reply_at',
    ];

    protected $casts = [
        'images' => 'array',
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'reply_at' => 'datetime',
    ];

    /**
     * Reviewable relationship (polymorphic).
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Reviewer relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Replier relationship.
     */
    public function replier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_by');
    }

    /**
     * Scope for approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope for verified purchases.
     */
    public function scopeVerifiedPurchase($query)
    {
        return $query->where('is_verified_purchase', true);
    }

    /**
     * Check if has reply.
     */
    public function hasReply(): bool
    {
        return !empty($this->reply_text);
    }
}
