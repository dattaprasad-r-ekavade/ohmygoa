<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'parent_id',
        'latitude',
        'longitude',
        'position',
        'is_popular',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    protected $slugSourceColumn = 'name';

    /**
     * Parent location relationship.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * Children locations relationship.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * Business listings relationship.
     */
    public function businessListings(): HasMany
    {
        return $this->hasMany(BusinessListing::class);
    }

    /**
     * Scope for active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for popular locations.
     */
    public function scopePopular($query)
    {
        return $query->where('is_popular', true);
    }

    /**
     * Scope for specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for states.
     */
    public function scopeStates($query)
    {
        return $query->where('type', 'state');
    }

    /**
     * Scope for cities.
     */
    public function scopeCities($query)
    {
        return $query->where('type', 'city');
    }

    /**
     * Get full location path.
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(', ', $path);
    }
}
