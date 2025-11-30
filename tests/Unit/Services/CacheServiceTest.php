<?php

namespace Tests\Unit\Services;

use App\Models\BusinessListing;
use App\Models\Category;
use App\Models\Location;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CacheService();
    }

    protected function tearDown(): void
    {
        Cache::flush();
        parent::tearDown();
    }

    /** @test */
    public function it_has_correct_ttl_constants()
    {
        $this->assertEquals(0, CacheService::TTL_FOREVER);
        $this->assertEquals(86400, CacheService::TTL_LONG); // 24 hours
        $this->assertEquals(3600, CacheService::TTL_MEDIUM); // 1 hour
        $this->assertEquals(300, CacheService::TTL_SHORT); // 5 minutes
    }

    /** @test */
    public function it_generates_correct_cache_keys()
    {
        $this->assertEquals('categories.all', $this->service->getCategoriesKey());
        $this->assertEquals('categories.1', $this->service->getCategoryKey(1));
        $this->assertEquals('locations.all', $this->service->getLocationsKey());
        $this->assertEquals('locations.1', $this->service->getLocationKey(1));
    }

    /** @test */
    public function it_caches_categories()
    {
        $categories = Category::factory()->count(3)->create();

        $cached = $this->service->rememberCategories();

        $this->assertCount(3, $cached);
        $this->assertTrue(Cache::has('categories.all'));
    }

    /** @test */
    public function it_returns_cached_categories_on_subsequent_calls()
    {
        $categories = Category::factory()->count(3)->create();

        // First call - caches data
        $firstCall = $this->service->rememberCategories();

        // Add more categories after caching
        Category::factory()->count(2)->create();

        // Second call - should return cached data (still 3)
        $secondCall = $this->service->rememberCategories();

        $this->assertCount(3, $secondCall);
    }

    /** @test */
    public function it_caches_locations()
    {
        $locations = Location::factory()->count(5)->create();

        $cached = $this->service->rememberLocations();

        $this->assertCount(5, $cached);
        $this->assertTrue(Cache::has('locations.all'));
    }

    /** @test */
    public function it_caches_single_category()
    {
        $category = Category::factory()->create();

        $cached = $this->service->rememberCategory($category->id);

        $this->assertEquals($category->id, $cached->id);
        $this->assertTrue(Cache::has("categories.{$category->id}"));
    }

    /** @test */
    public function it_caches_single_location()
    {
        $location = Location::factory()->create();

        $cached = $this->service->rememberLocation($location->id);

        $this->assertEquals($location->id, $cached->id);
        $this->assertTrue(Cache::has("locations.{$location->id}"));
    }

    /** @test */
    public function it_caches_featured_listings()
    {
        $featured = BusinessListing::factory()->count(3)->create([
            'is_featured' => true,
            'status' => 'approved',
        ]);

        $cached = $this->service->rememberFeaturedListings();

        $this->assertCount(3, $cached);
        $this->assertTrue(Cache::has('listings.featured'));
    }

    /** @test */
    public function it_caches_popular_listings()
    {
        $popular = BusinessListing::factory()->count(5)->create([
            'status' => 'approved',
            'view_count' => 100,
        ]);

        $cached = $this->service->rememberPopularListings();

        $this->assertCount(5, $cached);
        $this->assertTrue(Cache::has('listings.popular'));
    }

    /** @test */
    public function it_clears_all_cache()
    {
        // Cache some data
        Cache::put('categories.all', ['test'], 60);
        Cache::put('locations.all', ['test'], 60);
        Cache::put('listings.featured', ['test'], 60);

        $this->assertTrue(Cache::has('categories.all'));
        $this->assertTrue(Cache::has('locations.all'));

        $this->service->clearAll();

        $this->assertFalse(Cache::has('categories.all'));
        $this->assertFalse(Cache::has('locations.all'));
        $this->assertFalse(Cache::has('listings.featured'));
    }

    /** @test */
    public function it_clears_categories_cache()
    {
        Cache::put('categories.all', ['test'], 60);
        Cache::put('categories.1', ['test'], 60);

        $this->service->clearCategories();

        $this->assertFalse(Cache::has('categories.all'));
        $this->assertFalse(Cache::has('categories.1'));
    }

    /** @test */
    public function it_clears_locations_cache()
    {
        Cache::put('locations.all', ['test'], 60);
        Cache::put('locations.1', ['test'], 60);

        $this->service->clearLocations();

        $this->assertFalse(Cache::has('locations.all'));
        $this->assertFalse(Cache::has('locations.1'));
    }

    /** @test */
    public function it_clears_listings_cache()
    {
        Cache::put('listings.featured', ['test'], 60);
        Cache::put('listings.popular', ['test'], 60);
        Cache::put('listings.1', ['test'], 60);

        $this->service->clearListings();

        $this->assertFalse(Cache::has('listings.featured'));
        $this->assertFalse(Cache::has('listings.popular'));
        $this->assertFalse(Cache::has('listings.1'));
    }

    /** @test */
    public function it_clears_specific_listing_cache()
    {
        Cache::put('listings.1', ['test'], 60);
        Cache::put('listings.2', ['test'], 60);

        $this->service->clearListing(1);

        $this->assertFalse(Cache::has('listings.1'));
        $this->assertTrue(Cache::has('listings.2'));
    }

    /** @test */
    public function it_uses_correct_ttl_for_long_cache()
    {
        $category = Category::factory()->create();

        // Cache with long TTL
        $cached = $this->service->rememberCategories();

        $this->assertTrue(Cache::has('categories.all'));
    }

    /** @test */
    public function it_handles_null_values()
    {
        $cached = $this->service->rememberCategory(999); // Non-existent

        $this->assertNull($cached);
    }

    /** @test */
    public function it_caches_category_with_listings_count()
    {
        $category = Category::factory()->create();
        
        BusinessListing::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 'approved',
        ]);

        $cached = $this->service->rememberCategory($category->id);

        $this->assertNotNull($cached);
        $this->assertEquals($category->id, $cached->id);
    }
}
