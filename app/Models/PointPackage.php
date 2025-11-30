<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'points', 'price', 'bonus_points',
        'is_active', 'is_featured', 'display_order', 'description'
    ];

    protected $casts = [
        'points' => 'integer',
        'price' => 'decimal:2',
        'bonus_points' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    // Methods
    public function getTotalPointsAttribute()
    {
        return $this->points + $this->bonus_points;
    }

    public function getPricePerPointAttribute()
    {
        return $this->total_points > 0 ? $this->price / $this->total_points : 0;
    }
}
