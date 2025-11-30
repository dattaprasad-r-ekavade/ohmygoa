<?php

namespace App\Models;

use App\Traits\Bookmarkable;
use App\Traits\HasSlug;
use App\Traits\Reviewable;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceExpert extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Reviewable, Bookmarkable;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'business_name',
        'slug',
        'description',
        'services_offered',
        'service_areas',
        'phone',
        'email',
        'website',
        'certifications',
        'years_experience',
        'profile_image',
        'portfolio_images',
        'average_rating',
        'total_reviews',
        'jobs_completed',
        'is_verified',
        'is_available',
        'is_featured',
        'is_active',
        'status',
        'working_hours',
        'languages_spoken',
        'insurance_details',
        'hourly_rate',
        'minimum_charge',
        'offers_emergency_service',
        'response_time_hours',
        'completion_rate',
        'total_bookings',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'service_areas' => 'array',
        'certifications' => 'array',
        'portfolio_images' => 'array',
        'working_hours' => 'array',
        'average_rating' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'minimum_charge' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'offers_emergency_service' => 'boolean',
    ];

    protected $slugSourceColumn = 'business_name';
    protected $searchable = ['business_name', 'description'];

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
     * Bookings relationship.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ServiceBooking::class);
    }

    /**
     * Portfolio items relationship.
     */
    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class);
    }

    /**
     * Scope for active experts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for verified experts.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for available experts.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for featured experts.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
