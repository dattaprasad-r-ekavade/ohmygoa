<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'schema_markup',
        'canonical_url',
    ];

    protected $casts = [
        'meta_keywords' => 'array',
    ];

    /**
     * Seoable relationship (polymorphic).
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
