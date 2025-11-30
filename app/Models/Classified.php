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
        'price',
        'is_negotiable',
        'contact_name',
        'contact_phone',
        'contact_email',
        'address',
        'images',
        'condition',
        'status',
        'is_featured',
        'is_urgent',
        'expires_at',
        'views_count',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:2',
        'is_negotiable' => 'boolean',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'expires_at' => 'datetime',
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
     * Check if expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
