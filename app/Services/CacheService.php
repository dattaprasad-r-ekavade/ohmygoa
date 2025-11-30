<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Location;
use App\Models\BusinessListing;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    // Cache durations (in seconds)
    const CACHE_FOREVER = 86400 * 30; // 30 days
    const CACHE_LONG = 86400; // 24 hours
    const CACHE_MEDIUM = 3600; // 1 hour
    const CACHE_SHORT = 300; // 5 minutes

    /**
     * Get all active categories with caching
     */
    public function getCategories(string $type = null): \Illuminate\Support\Collection
    {
        $cacheKey = $type ? "categories.{$type}" : 'categories.all';
        
        return Cache::remember($cacheKey, self::CACHE_LONG, function () use ($type) {
            $query = Category::where('is_active', true)
                ->with('children')
                ->whereNull('parent_id')
                ->orderBy('display_order');
            
            if ($type) {
                $query->where('type', $type);
            }
            
            return $query->get();
        });
    }

    /**
     * Get category by slug with caching
     */
    public function getCategoryBySlug(string $slug): ?Category
    {
        return Cache::remember("category.{$slug}", self::CACHE_LONG, function () use ($slug) {
            return Category::where('slug', $slug)
                ->where('is_active', true)
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)->orderBy('display_order');
                }])
                ->first();
        });
    }

    /**
     * Get all locations with hierarchy
     */
    public function getLocations(string $type = null): \Illuminate\Support\Collection
    {
        $cacheKey = $type ? "locations.{$type}" : 'locations.all';
        
        return Cache::remember($cacheKey, self::CACHE_LONG, function () use ($type) {
            $query = Location::where('is_active', true)
                ->with('children')
                ->whereNull('parent_id')
                ->orderBy('display_order');
            
            if ($type) {
                $query->where('type', $type);
            }
            
            return $query->get();
        });
    }

    /**
     * Get popular locations with caching
     */
    public function getPopularLocations(int $limit = 10): \Illuminate\Support\Collection
    {
        return Cache::remember("locations.popular.{$limit}", self::CACHE_MEDIUM, function () use ($limit) {
            return Location::where('is_active', true)
                ->where('is_popular', true)
                ->orderBy('display_order')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get location by slug with caching
     */
    public function getLocationBySlug(string $slug): ?Location
    {
        return Cache::remember("location.{$slug}", self::CACHE_LONG, function () use ($slug) {
            return Location::where('slug', $slug)
                ->where('is_active', true)
                ->with(['children' => function ($query) {
                    $query->where('is_active', true)->orderBy('display_order');
                }])
                ->first();
        });
    }

    /**
     * Get featured listings with caching
     */
    public function getFeaturedListings(int $limit = 12): \Illuminate\Support\Collection
    {
        return Cache::remember("listings.featured.{$limit}", self::CACHE_SHORT, function () use ($limit) {
            return BusinessListing::where('status', 'approved')
                ->where('is_featured', true)
                ->with(['user', 'category', 'location', 'images'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular listings with caching
     */
    public function getPopularListings(int $limit = 12): \Illuminate\Support\Collection
    {
        return Cache::remember("listings.popular.{$limit}", self::CACHE_SHORT, function () use ($limit) {
            return BusinessListing::where('status', 'approved')
                ->with(['user', 'category', 'location', 'images'])
                ->orderBy('views', 'desc')
                ->orderBy('rating', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get recent listings with caching
     */
    public function getRecentListings(int $limit = 12): \Illuminate\Support\Collection
    {
        return Cache::remember("listings.recent.{$limit}", self::CACHE_SHORT, function () use ($limit) {
            return BusinessListing::where('status', 'approved')
                ->with(['user', 'category', 'location', 'images'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get top-rated listings with caching
     */
    public function getTopRatedListings(int $limit = 12): \Illuminate\Support\Collection
    {
        return Cache::remember("listings.top_rated.{$limit}", self::CACHE_SHORT, function () use ($limit) {
            return BusinessListing::where('status', 'approved')
                ->where('rating', '>=', 4.0)
                ->where('reviews_count', '>=', 5)
                ->with(['user', 'category', 'location', 'images'])
                ->orderBy('rating', 'desc')
                ->orderBy('reviews_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get application settings with caching
     */
    public function getSettings(string $group = null): \Illuminate\Support\Collection
    {
        $cacheKey = $group ? "settings.{$group}" : 'settings.all';
        
        return Cache::remember($cacheKey, self::CACHE_FOREVER, function () use ($group) {
            $query = Setting::query();
            
            if ($group) {
                $query->where('group', $group);
            }
            
            return $query->get()->pluck('value', 'key');
        });
    }

    /**
     * Get single setting value with caching
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting.{$key}", self::CACHE_FOREVER, function () use ($key, $default) {
            return Setting::get($key, $default);
        });
    }

    /**
     * Get dashboard statistics with caching
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard.stats', self::CACHE_SHORT, function () {
            return [
                'total_listings' => BusinessListing::where('status', 'approved')->count(),
                'total_users' => DB::table('users')->count(),
                'total_reviews' => DB::table('reviews')->where('status', 'approved')->count(),
                'total_categories' => Category::where('is_active', true)->whereNull('parent_id')->count(),
                'total_locations' => Location::where('is_active', true)->count(),
                'pending_listings' => BusinessListing::where('status', 'pending')->count(),
                'pending_reviews' => DB::table('reviews')->where('status', 'pending')->count(),
            ];
        });
    }

    /**
     * Clear specific cache
     */
    public function clearCache(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Clear all categories cache
     */
    public function clearCategoriesCache(): void
    {
        Cache::forget('categories.all');
        
        foreach (['business', 'listing', 'event', 'job', 'product', 'service'] as $type) {
            Cache::forget("categories.{$type}");
        }
    }

    /**
     * Clear all locations cache
     */
    public function clearLocationsCache(): void
    {
        Cache::forget('locations.all');
        Cache::tags(['locations'])->flush();
        
        foreach (['country', 'state', 'district', 'city', 'area'] as $type) {
            Cache::forget("locations.{$type}");
        }
    }

    /**
     * Clear all listings cache
     */
    public function clearListingsCache(): void
    {
        Cache::tags(['listings'])->flush();
        
        for ($i = 1; $i <= 20; $i++) {
            Cache::forget("listings.featured.{$i}");
            Cache::forget("listings.popular.{$i}");
            Cache::forget("listings.recent.{$i}");
            Cache::forget("listings.top_rated.{$i}");
        }
    }

    /**
     * Clear all settings cache
     */
    public function clearSettingsCache(): void
    {
        Cache::forget('settings.all');
        Cache::tags(['settings'])->flush();
    }

    /**
     * Clear dashboard stats cache
     */
    public function clearDashboardStatsCache(): void
    {
        Cache::forget('dashboard.stats');
    }

    /**
     * Clear all application cache
     */
    public function clearAllCache(): void
    {
        Cache::flush();
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public function warmUpCache(): void
    {
        // Warm up categories
        $this->getCategories();
        foreach (['business', 'event', 'job', 'product'] as $type) {
            $this->getCategories($type);
        }

        // Warm up locations
        $this->getLocations();
        $this->getPopularLocations();

        // Warm up listings
        $this->getFeaturedListings();
        $this->getPopularListings();
        $this->getRecentListings();
        $this->getTopRatedListings();

        // Warm up settings
        $this->getSettings();

        // Warm up stats
        $this->getDashboardStats();
    }
}
