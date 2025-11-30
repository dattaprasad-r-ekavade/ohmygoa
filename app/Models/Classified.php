<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasViewCount;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classified extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, HasViewCount;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'title',
        'slug',
        'description',
        'ad_type',
        'listing_type',
        'price',
        'is_negotiable',
        'accepts_exchange',
        'exchange_preferences',
        'contact_name',
        'contact_phone',
        'contact_email',
        'address',
        'images',
        'condition',
        'brand',
        'model',
        'year',
        'specifications',
        'quantity',
        'status',
        'is_featured',
        'is_urgent',
        'expires_at',
        'featured_until',
        'bumped_at',
        'views_count',
        'total_inquiries',
    ];

    protected $casts = [
        'images' => 'array',
        'specifications' => 'array',
        'price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'accepts_exchange' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'expires_at' => 'datetime',
        'featured_until' => 'datetime',
        'bumped_at' => 'datetime',
    ];

    protected $slugSourceColumn = 'title';
    protected $searchable = ['title', 'description'];

    /**
     * User relationship.
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
     * Scope for active classifieds.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for featured classifieds.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for urgent classifieds.
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope by ad type.
     */
    public function scopeByAdType($query, string $adType)
    {
        return $query->where('ad_type', $adType);
    }

    /**
     * Scope by listing type.
     */
    public function scopeByListingType($query, string $listingType)
    {
        return $query->where('listing_type', $listingType);
    }

    /**
     * Check if expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if featured and still valid.
     */
    public function isFeaturedActive(): bool
    {
        return $this->is_featured && 
               ($this->featured_until === null || $this->featured_until->isFuture());
    }

    /**
     * Can be bumped (once per 24 hours).
     */
    public function canBeBumped(): bool
    {
        return $this->bumped_at === null || $this->bumped_at->addHours(24)->isPast();
    }

    /**
     * Bump the ad to top.
     */
    public function bump(): void
    {
        $this->update(['bumped_at' => now()]);
    }

    /**
     * Increment inquiry count.
     */
    public function incrementInquiries(): void
    {
        $this->increment('total_inquiries');
    }

    /**
     * Get available ad types.
     */
    public static function getAdTypes(): array
    {
        return [
            'sell' => 'For Sale',
            'buy' => 'Wanted to Buy',
            'rent' => 'For Rent',
            'service' => 'Service Offered',
        ];
    }

    /**
     * Get available listing types.
     */
    public static function getListingTypes(): array
    {
        return [
            'free' => 'Free Listing',
            'featured' => 'Featured Listing',
            'premium' => 'Premium Listing',
        ];
    }

    /**
     * Get available conditions.
     */
    public static function getConditions(): array
    {
        return [
            'new' => 'Brand New',
            'like_new' => 'Like New',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
        ];
    }
}
