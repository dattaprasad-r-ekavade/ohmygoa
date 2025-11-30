<?php

namespace App\Models;

use App\Traits\Bookmarkable;
use App\Traits\HasSlug;
use App\Traits\HasViewCount;
use App\Traits\Reviewable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessListing extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Reviewable, Bookmarkable, HasViewCount;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'business_name',
        'slug',
        'description',
        'address',
        'phone',
        'email',
        'website',
        'latitude',
        'longitude',
        'business_hours',
        'social_media',
        'logo',
        'banner_image',
        'average_rating',
        'total_reviews',
        'views_count',
        'is_verified',
        'is_featured',
        'is_active',
        'status',
        'verified_at',
        'featured_until',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'social_media' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'average_rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'verified_at' => 'datetime',
        'featured_until' => 'datetime',
    ];

    protected $slugSourceColumn = 'business_name';
    protected $searchable = ['business_name', 'description', 'address'];

    /**
     * Owner relationship.
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
     * Images relationship.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class);
    }

    /**
     * Followers relationship.
     */
    public function followers(): HasMany
    {
        return $this->hasMany(Follow::class);
    }

    /**
     * Coupons relationship.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * SEO metadata relationship.
     */
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Enquiries relationship.
     */
    public function enquiries()
    {
        return $this->morphMany(Enquiry::class, 'enquirable');
    }

    /**
     * Scope for active listings.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified listings.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for featured listings.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }

    /**
     * Scope for approved listings.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending listings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Check if listing is featured.
     */
    public function isFeatured(): bool
    {
        return $this->is_featured && 
               ($this->featured_until === null || $this->featured_until->isFuture());
    }

    /**
     * Check if followed by user.
     */
    public function isFollowedBy($userId): bool
    {
        return $this->followers()->where('user_id', $userId)->exists();
    }
}
