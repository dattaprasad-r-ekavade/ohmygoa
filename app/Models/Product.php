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
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Reviewable, Bookmarkable, HasViewCount;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'stock_status',
        'specifications',
        'brand',
        'main_image',
        'gallery',
        'weight',
        'dimensions',
        'is_featured',
        'is_active',
        'status',
        'average_rating',
        'total_reviews',
        'views_count',
        'sales_count',
    ];

    protected $casts = [
        'specifications' => 'array',
        'gallery' => 'array',
        'dimensions' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $slugSourceColumn = 'name';
    protected $searchable = ['name', 'description', 'brand', 'sku'];

    /**
     * Seller relationship.
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
     * SEO metadata relationship.
     */
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Scope for active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for approved products.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for in stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_status', 'in_stock');
    }

    /**
     * Scope for featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for on sale products.
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price');
    }

    /**
     * Get effective price.
     */
    public function getEffectivePriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    /**
     * Check if on sale.
     */
    public function isOnSale(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * Get discount percentage.
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->isOnSale()) {
            return null;
        }

        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Check if in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_status === 'in_stock' && $this->stock_quantity > 0;
    }
}
