<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'query', 'type', 'filters', 'results_count', 'ip_address'
    ];

    protected $casts = [
        'filters' => 'array'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static methods
    public static function logSearch($query, $type = null, $filters = [], $resultsCount = 0, $userId = null)
    {
        return static::create([
            'user_id' => $userId,
            'query' => $query,
            'type' => $type,
            'filters' => $filters,
            'results_count' => $resultsCount,
            'ip_address' => request()->ip()
        ]);
    }

    public static function popularSearches($limit = 10, $type = null)
    {
        $query = static::selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit($limit);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    public static function recentSearches($limit = 10, $type = null)
    {
        $query = static::selectRaw('DISTINCT query, MAX(created_at) as latest')
            ->groupBy('query')
            ->orderBy('latest', 'desc')
            ->limit($limit);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }
}
