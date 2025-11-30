<?php

namespace App\Traits;

use App\Models\Bookmark;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Bookmarkable
{
    /**
     * Get all bookmarks for the model.
     */
    public function bookmarks(): MorphMany
    {
        return $this->morphMany(Bookmark::class, 'bookmarkable');
    }

    /**
     * Check if bookmarked by user.
     */
    public function isBookmarkedBy($userId): bool
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    /**
     * Toggle bookmark for user.
     */
    public function toggleBookmark($userId): bool
    {
        $bookmark = $this->bookmarks()->where('user_id', $userId)->first();

        if ($bookmark) {
            $bookmark->delete();
            return false;
        }

        $this->bookmarks()->create(['user_id' => $userId]);
        return true;
    }

    /**
     * Get bookmark count.
     */
    public function getBookmarkCountAttribute(): int
    {
        return $this->bookmarks()->count();
    }
}
