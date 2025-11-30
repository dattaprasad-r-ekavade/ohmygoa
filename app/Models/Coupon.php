<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasViewCount;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, HasViewCount;

    protected $fillable = [
        'user_id',
        'business_listing_id',
        'title',
        'code',
        'description',
        'terms_conditions',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'start_date',
        'end_date',
        'image',
        'is_featured',
        'is_active',
        'status',
        'redemptions_count',
        'views_count',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $slugSourceColumn = 'title';
    protected $searchable = ['title', 'code', 'description'];

    /**
     * Creator relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Business listing relationship.
     */
    public function businessListing(): BelongsTo
    {
        return $this->belongsTo(BusinessListing::class);
    }

    /**
     * Redemptions relationship.
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(CouponRedemption::class);
    }

    /**
     * Scope for active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('status', 'approved')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope for featured coupons.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(): bool
    {
        return $this->is_active &&
               $this->status === 'approved' &&
               $this->start_date->isPast() &&
               $this->end_date->isFuture() &&
               ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Check if user can redeem.
     */
    public function canBeRedeemedBy($userId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->per_user_limit === null) {
            return true;
        }

        $userRedemptions = $this->redemptions()->where('user_id', $userId)->count();
        return $userRedemptions < $this->per_user_limit;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount($amount): float
    {
        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? ($amount * $this->discount_value / 100)
            : $this->discount_value;

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return round($discount, 2);
    }
}
