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
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceExpert extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Reviewable, Bookmarkable, HasViewCount;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'business_name',
        'slug',
        'description',
        'services_offered',
        'service_areas',
        'contact_phone',
        'contact_email',
        'address',
        'website',
        'certifications',
        'skills',
        'languages_spoken',
        'working_hours',
        'insurance_details',
        'availability',
        'years_of_experience',
        'profile_image',
        'portfolio_images',
        'average_rating',
        'total_reviews',
        'jobs_completed',
        'hourly_rate',
        'minimum_charge',
        'is_verified',
        'is_available',
        'is_featured',
        'is_active',
        'status',
        'offers_emergency_service',
        'response_time_hours',
        'completion_rate',
        'total_bookings',
        'views_count',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'service_areas' => 'array',
        'certifications' => 'array',
        'skills' => 'array',
        'portfolio_images' => 'array',
        'working_hours' => 'array',
        'average_rating' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'minimum_charge' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'offers_emergency_service' => 'boolean',
        'languages_spoken' => 'array',
        'years_of_experience' => 'integer',
        'response_time_hours' => 'integer',
        'total_bookings' => 'integer',
        'jobs_completed' => 'integer',
        'views_count' => 'integer',
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
