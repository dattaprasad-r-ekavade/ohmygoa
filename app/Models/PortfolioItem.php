<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_expert_id',
        'title',
        'description',
        'images',
        'project_type',
        'completion_date',
        'client_name',
        'is_featured',
        'order_number',
    ];

    protected $casts = [
        'images' => 'array',
        'completion_date' => 'date',
        'is_featured' => 'boolean',
        'order_number' => 'integer',
    ];

    /**
     * Get the service expert for this portfolio item.
     */
    public function serviceExpert(): BelongsTo
    {
        return $this->belongsTo(ServiceExpert::class);
    }

    /**
     * Scope a query to only include featured items.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by order number.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_number')->orderBy('created_at', 'desc');
    }

    /**
     * Get the first image from the images array.
     */
    public function getFirstImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }
}
