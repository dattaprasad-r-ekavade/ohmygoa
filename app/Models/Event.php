<?php

namespace App\Models;

use App\Traits\Bookmarkable;
use App\Traits\HasSlug;
use App\Traits\HasViewCount;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Bookmarkable, HasViewCount;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'title',
        'slug',
        'description',
        'short_description',
        'venue',
        'address',
        'latitude',
        'longitude',
        'start_date',
        'end_date',
        'organizer_name',
        'organizer_email',
        'organizer_phone',
        'image',
        'gallery',
        'price',
        'is_free',
        'total_seats',
        'available_seats',
        'status',
        'is_featured',
        'is_active',
        'views_count',
    ];

    protected $casts = [
        'gallery' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'is_free' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $slugSourceColumn = 'title';
    protected $searchable = ['title', 'description', 'venue'];

    /**
     * Organizer relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Location relationship.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * SEO metadata relationship.
     */
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
            ->where('start_date', '>', now());
    }

    /**
     * Scope for ongoing events.
     */
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope for active events.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured events.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if event is bookable.
     */
    public function isBookable(): bool
    {
        return $this->status === 'upcoming' && 
               $this->start_date->isFuture() &&
               ($this->available_seats === null || $this->available_seats > 0);
    }

    /**
     * Check if event is full.
     */
    public function isFull(): bool
    {
        return $this->available_seats !== null && $this->available_seats <= 0;
    }
}
