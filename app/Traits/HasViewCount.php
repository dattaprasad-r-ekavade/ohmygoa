<?php

namespace App\Traits;

trait HasViewCount
{
    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('views_count');
    }

    /**
     * Scope for most viewed.
     */
    public function scopeMostViewed($query, $limit = 10)
    {
        return $query->orderBy('views_count', 'desc')->limit($limit);
    }
}
