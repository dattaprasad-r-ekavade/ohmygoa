<?php

namespace App\Traits;

use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Reviewable
{
    /**
     * Get all reviews for the model.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get approved reviews.
     */
    public function approvedReviews(): MorphMany
    {
        return $this->reviews()->where('is_approved', true);
    }

    /**
     * Update average rating.
     */
    public function updateAverageRating(): void
    {
        $average = $this->approvedReviews()->avg('rating');
        $total = $this->approvedReviews()->count();

        $this->update([
            'average_rating' => round($average, 2),
            'total_reviews' => $total,
        ]);
    }

    /**
     * Check if reviewed by user.
     */
    public function isReviewedBy($userId): bool
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }

    /**
     * Get review by user.
     */
    public function getReviewByUser($userId)
    {
        return $this->reviews()->where('user_id', $userId)->first();
    }
}
