# Performance Optimization Guide

## Overview
This guide covers all performance optimizations implemented in the Ohmygoa platform to ensure fast page loads, efficient database queries, and optimal user experience.

## 1. Caching Strategy

### CacheService Implementation
The `CacheService` class provides centralized caching for all frequently accessed data with different TTL (Time To Live) values based on data volatility:

- **CACHE_FOREVER** (30 days): Settings, static configuration
- **CACHE_LONG** (24 hours): Categories, locations, navigation data
- **CACHE_MEDIUM** (1 hour): Popular locations, statistics
- **CACHE_SHORT** (5 minutes): Dynamic listings, dashboard data

### Cached Data Types

#### Categories
```php
$cacheService = new CacheService();

// Get all categories (cached for 24 hours)
$categories = $cacheService->getCategories();

// Get categories by type
$businessCategories = $cacheService->getCategories('business');
$eventCategories = $cacheService->getCategories('event');
```

#### Locations
```php
// Get all locations with hierarchy
$locations = $cacheService->getLocations();

// Get popular locations
$popularLocations = $cacheService->getPopularLocations(10);

// Get specific location
$location = $cacheService->getLocationBySlug('calangute');
```

#### Listings
```php
// Get featured listings (cached for 5 minutes)
$featured = $cacheService->getFeaturedListings(12);

// Get popular listings (by views and rating)
$popular = $cacheService->getPopularListings(12);

// Get recent listings
$recent = $cacheService->getRecentListings(12);

// Get top-rated listings
$topRated = $cacheService->getTopRatedListings(12);
```

#### Settings
```php
// Get all settings
$settings = $cacheService->getSettings();

// Get settings by group
$pointSettings = $cacheService->getSettings('points');

// Get single setting
$value = $cacheService->getSetting('site_name', 'Ohmygoa');
```

### Cache Invalidation

#### Manual Cache Clearing
```php
$cacheService = new CacheService();

// Clear specific cache
$cacheService->clearCache('categories.all');

// Clear all categories cache
$cacheService->clearCategoriesCache();

// Clear all locations cache
$cacheService->clearLocationsCache();

// Clear all listings cache
$cacheService->clearListingsCache();

// Clear all settings cache
$cacheService->clearSettingsCache();

// Clear dashboard stats
$cacheService->clearDashboardStatsCache();

// Clear everything
$cacheService->clearAllCache();
```

#### Automatic Cache Invalidation
Implement cache invalidation in model observers:

```php
// app/Observers/CategoryObserver.php
class CategoryObserver
{
    public function __construct(private CacheService $cacheService) {}

    public function saved(Category $category)
    {
        $this->cacheService->clearCategoriesCache();
    }

    public function deleted(Category $category)
    {
        $this->cacheService->clearCategoriesCache();
    }
}
```

### Cache Warmup Command

Warm up cache with frequently accessed data:

```bash
# Warm up cache
php artisan cache:warmup

# Clear and warm up cache
php artisan cache:warmup --clear
```

Schedule cache warmup in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Warm up cache every hour
    $schedule->command('cache:warmup')->hourly();
    
    // Clear and warm up cache daily at 3 AM
    $schedule->command('cache:warmup --clear')->dailyAt('03:00');
}
```

## 2. Database Optimization

### Indexes Added

#### Business Listings
- `idx_listings_featured`: (status, is_featured, created_at)
- `idx_listings_popular`: (status, views, rating)
- `idx_listings_rated`: (status, rating, reviews_count)
- `idx_listings_category`: (category_id, status)
- `idx_listings_location`: (location_id, status)

#### Reviews
- `idx_reviews_polymorphic`: (reviewable_type, reviewable_id, status)
- `idx_reviews_user`: (user_id, status)

#### Events
- `idx_events_upcoming`: (status, start_date)
- `idx_events_category`: (category_id, status)

#### Job Listings
- `idx_jobs_active`: (status, deadline)
- `idx_jobs_category`: (category_id, status)

#### Products
- `idx_products_featured`: (status, is_featured)
- `idx_products_category`: (category_id, status)

#### Payments
- `idx_payments_user`: (user_id, status)
- `idx_payments_type`: (type, status)

### Eager Loading

Always use eager loading to prevent N+1 query problems:

```php
// ❌ Bad - N+1 queries
$listings = BusinessListing::all();
foreach ($listings as $listing) {
    echo $listing->user->name; // Separate query for each listing
    echo $listing->category->name; // Another query
}

// ✅ Good - Single query with eager loading
$listings = BusinessListing::with(['user', 'category', 'location', 'images'])
    ->get();
```

### Query Optimization Examples

#### Homepage Featured Listings
```php
// Optimized query with caching
$featured = Cache::remember('listings.featured', 300, function () {
    return BusinessListing::where('status', 'approved')
        ->where('is_featured', true)
        ->with(['user:id,name', 'category:id,name,slug', 'location:id,name', 'images' => function ($query) {
            $query->limit(1);
        }])
        ->select('id', 'user_id', 'category_id', 'location_id', 'title', 'slug', 'rating', 'views', 'price')
        ->orderBy('created_at', 'desc')
        ->limit(12)
        ->get();
});
```

#### Search with Filters
```php
$results = BusinessListing::query()
    ->where('status', 'approved')
    ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
    ->when($locationId, fn($q) => $q->where('location_id', $locationId))
    ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
    ->with(['user:id,name', 'category:id,name', 'location:id,name'])
    ->select('id', 'user_id', 'category_id', 'location_id', 'title', 'slug', 'rating', 'price')
    ->paginate(20);
```

### Query Chunking for Large Datasets

For operations on large datasets, use chunking:

```php
// Process 1000 records at a time
BusinessListing::where('status', 'approved')
    ->chunk(1000, function ($listings) {
        foreach ($listings as $listing) {
            // Process each listing
        }
    });
```

## 3. Asset Optimization

### Laravel Mix Configuration

Update `webpack.mix.js` for asset optimization:

```javascript
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('tailwindcss'),
       require('autoprefixer'),
   ])
   .version() // Cache busting
   .sourceMaps(false, 'source-map'); // Disable source maps in production

if (mix.inProduction()) {
    mix.minify('public/js/app.js')
       .minify('public/css/app.css');
}
```

### Image Optimization

#### Image Upload Optimization
The `SecureFileUploadService` already optimizes images on upload:

```php
// Automatic image optimization
$service = new SecureFileUploadService();
$path = $service->uploadImage($file, 'listings');

// Images are:
// - Resized to max 1920x1080
// - Compressed to 80% quality
// - Converted to WebP format (if supported)
```

#### Lazy Loading Images
Use native lazy loading for images:

```blade
<img src="{{ $listing->image_url }}" 
     alt="{{ $listing->title }}"
     loading="lazy"
     class="w-full h-auto">
```

#### Responsive Images
Serve different image sizes for different screen sizes:

```blade
<img srcset="{{ $listing->thumbnail_url }} 320w,
             {{ $listing->medium_url }} 640w,
             {{ $listing->large_url }} 1024w"
     sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
     src="{{ $listing->medium_url }}"
     alt="{{ $listing->title }}"
     loading="lazy">
```

## 4. View Optimization

### Route Caching
Cache routes for faster route registration:

```bash
# Cache routes (production only)
php artisan route:cache

# Clear route cache
php artisan route:clear
```

### Config Caching
Cache configuration files:

```bash
# Cache config
php artisan config:cache

# Clear config cache
php artisan config:clear
```

### View Caching
Compile Blade templates:

```bash
# Compile views
php artisan view:cache

# Clear compiled views
php artisan view:clear
```

### View Composers
Register view composers for shared data instead of passing it from every controller:

```php
// app/Providers/ViewServiceProvider.php
use Illuminate\Support\Facades\View;
use App\Services\CacheService;

public function boot()
{
    View::composer('layouts.app', function ($view) {
        $cacheService = app(CacheService::class);
        
        $view->with([
            'categories' => $cacheService->getCategories(),
            'locations' => $cacheService->getPopularLocations(),
        ]);
    });
}
```

## 5. Queue Optimization

### Queue Configuration
Configure queue workers for optimal performance:

```env
QUEUE_CONNECTION=database
QUEUE_FAILED_DRIVER=database

# For production with Redis:
# QUEUE_CONNECTION=redis
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

### Queue Workers
Run queue workers with optimal settings:

```bash
# Development
php artisan queue:work --tries=3 --timeout=60

# Production (with Supervisor)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --memory=512
```

### Supervisor Configuration
```ini
[program:ohmygoa-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ohmygoa/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ohmygoa/storage/logs/worker.log
stopwaitsecs=3600
```

## 6. CDN Integration

### Asset URL Configuration
Configure CDN for static assets:

```env
# .env
ASSET_URL=https://cdn.ohmygoa.com
```

### Using Asset Helper
```blade
{{-- Assets will automatically use CDN URL --}}
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<script src="{{ asset('js/app.js') }}"></script>
```

## 7. HTTP Caching

### Browser Caching Headers
Configure nginx for browser caching:

```nginx
# Cache static assets
location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    add_header Cache-Control "public, immutable";
}

# Cache HTML with revalidation
location / {
    add_header Cache-Control "public, max-age=3600, must-revalidate";
}
```

### Response Caching Middleware
Create middleware for HTTP response caching:

```php
// app/Http/Middleware/CacheResponse.php
public function handle($request, Closure $next, $ttl = 3600)
{
    if ($request->method() !== 'GET') {
        return $next($request);
    }

    $key = 'http_cache:' . md5($request->fullUrl());

    if (Cache::has($key)) {
        return response(Cache::get($key))
            ->header('X-Cache', 'HIT');
    }

    $response = $next($request);
    
    if ($response->isSuccessful()) {
        Cache::put($key, $response->getContent(), $ttl);
    }

    return $response->header('X-Cache', 'MISS');
}
```

## 8. Performance Monitoring

### Query Logging (Development Only)
Enable query logging in development:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

public function boot()
{
    if (app()->environment('local')) {
        DB::listen(function ($query) {
            Log::info(
                $query->sql,
                [
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]
            );
        });
    }
}
```

### Performance Testing Commands
```bash
# Test database performance
php artisan tinker
>>> DB::enableQueryLog();
>>> BusinessListing::with('user', 'category')->get();
>>> DB::getQueryLog();

# Profile specific routes
php artisan route:list --path=listings

# Monitor queue performance
php artisan queue:monitor redis:default --max=100
```

## 9. Production Optimizations

### Optimization Commands
Run these commands before deploying to production:

```bash
# Optimize everything
php artisan optimize

# Individual optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Warm up cache
php artisan cache:warmup
```

### Clear Cache When Deploying
```bash
# Clear all caches
php artisan optimize:clear

# Or individually
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Deployment Script
```bash
#!/bin/bash
cd /var/www/ohmygoa

# Pull latest code
git pull origin master

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear and cache everything
php artisan optimize:clear
php artisan optimize
php artisan cache:warmup

# Restart queue workers
php artisan queue:restart

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```

## 10. Performance Checklist

### Development
- [ ] Use eager loading for relationships
- [ ] Implement caching for frequently accessed data
- [ ] Add database indexes for common queries
- [ ] Optimize images on upload
- [ ] Use lazy loading for images
- [ ] Queue long-running tasks
- [ ] Profile slow queries

### Production
- [ ] Enable OPcache for PHP
- [ ] Run `php artisan optimize`
- [ ] Configure CDN for static assets
- [ ] Set up Redis for caching and sessions
- [ ] Configure HTTP caching headers
- [ ] Enable gzip compression
- [ ] Minimize and bundle assets
- [ ] Set up queue workers with Supervisor
- [ ] Schedule cache warmup
- [ ] Monitor application performance

## Best Practices

1. **Always use caching for static/semi-static data**: Categories, locations, settings
2. **Implement eager loading**: Prevent N+1 query problems
3. **Add indexes strategically**: On columns used in WHERE, ORDER BY, JOIN clauses
4. **Queue heavy operations**: Emails, image processing, reports
5. **Use pagination**: Don't load all records at once
6. **Optimize images**: Compress, resize, lazy load
7. **Cache HTTP responses**: For public pages
8. **Monitor performance**: Log slow queries, use profiling tools
9. **Test with production data**: Performance issues often only appear with real data volume
10. **Use CDN**: Serve static assets from CDN to reduce server load

## Performance Metrics

Expected performance targets:
- **Homepage load time**: < 1 second
- **Search results**: < 500ms
- **Listing detail page**: < 800ms
- **Database queries**: < 100ms average
- **API response time**: < 200ms
- **Image load time**: < 2 seconds (with lazy loading)

Use tools like:
- Laravel Debugbar (development)
- Laravel Telescope (development/staging)
- New Relic / Datadog (production)
- Google PageSpeed Insights
- GTmetrix
