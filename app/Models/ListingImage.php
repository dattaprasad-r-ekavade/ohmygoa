<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_listing_id',
        'image_path',
        'thumbnail_path',
        'position',
        'is_primary',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Business listing relationship.
     */
    public function businessListing(): BelongsTo
    {
        return $this->belongsTo(BusinessListing::class);
    }

    /**
     * Scope for primary image.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
