<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'category',
        'is_breaking',
        'is_featured',
        'status',
        'source',
        'author_name',
        'user_id',
        'location_id',
        'view_count',
        'published_at',
        'rejection_reason',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    /**
     * Get the searchable columns for the model.
     */
    public function getSearchableColumns(): array
    {
        return ['title', 'excerpt', 'content', 'category', 'author_name'];
    }

    /**
     * Get the field to generate slug from.
     */
    public function getSlugSourceField(): string
    {
        return 'title';
    }

    /**
     * Get the user who created the news.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the location associated with the news.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Scope a query to only include published news.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include breaking news.
     */
    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    /**
     * Scope a query to only include featured news.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Check if the news is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    /**
     * Get the formatted published date.
     */
    public function getFormattedPublishedDateAttribute(): ?string
    {
        return $this->published_at?->format('M d, Y');
    }

    /**
     * Get available news categories.
     */
    public static function getCategories(): array
    {
        return [
            'tourism' => 'Tourism',
            'business' => 'Business',
            'culture' => 'Culture',
            'events' => 'Events',
            'general' => 'General',
        ];
    }
}
