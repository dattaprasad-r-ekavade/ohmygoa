<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\Reviewable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Reviewable;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'highlights',
        'category',
        'location_id',
        'address',
        'latitude',
        'longitude',
        'images',
        'featured_image',
        'timings',
        'entry_fee',
        'best_time_to_visit',
        'how_to_reach',
        'facilities',
        'contact_info',
        'average_rating',
        'total_reviews',
        'view_count',
        'is_featured',
        'is_popular',
        'is_active',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'images' => 'array',
        'timings' => 'array',
        'facilities' => 'array',
        'contact_info' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'average_rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the searchable columns for the model.
     */
    public function getSearchableColumns(): array
    {
        return ['name', 'description', 'highlights', 'category', 'address'];
    }

    /**
     * Get the field to generate slug from.
     */
    public function getSlugSourceField(): string
    {
        return 'name';
    }

    /**
     * Get the location for this place.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Scope a query to only include active places.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'approved');
    }

    /**
     * Scope a query to only include featured places.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include popular places.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by location.
     */
    public function scopeByLocation($query, int $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Get the first image from the images array.
     */
    public function getFirstImageAttribute(): ?string
    {
        return $this->images[0] ?? $this->featured_image;
    }

    /**
     * Check if the place has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Get available place categories.
     */
    public static function getCategories(): array
    {
        return [
            'beach' => 'Beaches',
            'church' => 'Churches',
            'temple' => 'Temples',
            'fort' => 'Forts & Heritage',
            'museum' => 'Museums',
            'waterfall' => 'Waterfalls',
            'viewpoint' => 'Viewpoints',
            'market' => 'Markets',
            'wildlife' => 'Wildlife',
            'other' => 'Other Attractions',
        ];
    }

    /**
     * Get available facilities.
     */
    public static function getFacilities(): array
    {
        return [
            'parking' => 'Parking',
            'restroom' => 'Restroom',
            'food' => 'Food & Beverages',
            'wheelchair' => 'Wheelchair Accessible',
            'wifi' => 'WiFi',
            'guide' => 'Tour Guide Available',
            'photography' => 'Photography Allowed',
            'water_sports' => 'Water Sports',
        ];
    }
}
