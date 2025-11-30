<?php

namespace Tests\Unit\Models;

use App\Models\BusinessListing;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $listing->user);
        $this->assertEquals($user->id, $listing->user->id);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $listing = BusinessListing::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $listing->category);
        $this->assertEquals($category->id, $listing->category->id);
    }

    /** @test */
    public function it_belongs_to_a_location()
    {
        $location = Location::factory()->create();
        $listing = BusinessListing::factory()->create(['location_id' => $location->id]);

        $this->assertInstanceOf(Location::class, $listing->location);
        $this->assertEquals($location->id, $listing->location->id);
    }

    /** @test */
    public function it_has_many_reviews()
    {
        $listing = BusinessListing::factory()->create();
        
        Review::factory()->count(3)->create([
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);

        $this->assertCount(3, $listing->reviews);
        $this->assertInstanceOf(Review::class, $listing->reviews->first());
    }

    /** @test */
    public function it_generates_unique_slug()
    {
        $listing1 = BusinessListing::factory()->create(['business_name' => 'Test Business']);
        $listing2 = BusinessListing::factory()->create(['business_name' => 'Test Business']);

        $this->assertNotEquals($listing1->slug, $listing2->slug);
        $this->assertStringContainsString('test-business', $listing1->slug);
        $this->assertStringContainsString('test-business', $listing2->slug);
    }

    /** @test */
    public function it_can_be_approved()
    {
        $listing = BusinessListing::factory()->create(['status' => 'pending']);

        $this->assertEquals('pending', $listing->status);
        
        $listing->update(['status' => 'approved']);
        
        $this->assertEquals('approved', $listing->fresh()->status);
    }

    /** @test */
    public function it_can_be_rejected()
    {
        $listing = BusinessListing::factory()->create(['status' => 'pending']);

        $listing->update(['status' => 'rejected']);
        
        $this->assertEquals('rejected', $listing->fresh()->status);
    }

    /** @test */
    public function it_calculates_average_rating()
    {
        $listing = BusinessListing::factory()->create(['average_rating' => 0, 'total_reviews' => 0]);

        Review::factory()->create([
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 4,
        ]);

        $averageRating = $listing->reviews()->avg('rating');
        $totalReviews = $listing->reviews()->count();

        $listing->update([
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
        ]);

        $this->assertEquals(4.5, $listing->fresh()->average_rating);
        $this->assertEquals(2, $listing->fresh()->total_reviews);
    }

    /** @test */
    public function it_tracks_view_count()
    {
        $listing = BusinessListing::factory()->create(['view_count' => 0]);

        $listing->increment('view_count');
        $this->assertEquals(1, $listing->fresh()->view_count);

        $listing->increment('view_count', 5);
        $this->assertEquals(6, $listing->fresh()->view_count);
    }

    /** @test */
    public function it_can_be_featured()
    {
        $listing = BusinessListing::factory()->create([
            'is_featured' => false,
            'featured_until' => null,
        ]);

        $this->assertFalse($listing->is_featured);

        $listing->update([
            'is_featured' => true,
            'featured_until' => now()->addDays(30),
        ]);

        $this->assertTrue($listing->fresh()->is_featured);
        $this->assertNotNull($listing->fresh()->featured_until);
    }

    /** @test */
    public function it_checks_if_featured_period_is_active()
    {
        $activeFeatured = BusinessListing::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->addDays(10),
        ]);

        $expiredFeatured = BusinessListing::factory()->create([
            'is_featured' => true,
            'featured_until' => now()->subDay(),
        ]);

        $this->assertTrue($activeFeatured->is_featured && $activeFeatured->featured_until->isFuture());
        $this->assertTrue($expiredFeatured->is_featured && $expiredFeatured->featured_until->isPast());
    }

    /** @test */
    public function it_has_business_hours()
    {
        $hours = [
            'monday' => ['open' => '09:00', 'close' => '18:00'],
            'tuesday' => ['open' => '09:00', 'close' => '18:00'],
        ];

        $listing = BusinessListing::factory()->create(['business_hours' => $hours]);

        $this->assertEquals($hours, $listing->business_hours);
        $this->assertIsArray($listing->business_hours);
    }

    /** @test */
    public function it_has_contact_information()
    {
        $listing = BusinessListing::factory()->create([
            'contact_phone' => '9876543210',
            'contact_email' => 'business@example.com',
            'website' => 'https://example.com',
        ]);

        $this->assertEquals('9876543210', $listing->contact_phone);
        $this->assertEquals('business@example.com', $listing->contact_email);
        $this->assertEquals('https://example.com', $listing->website);
    }

    /** @test */
    public function it_has_location_coordinates()
    {
        $listing = BusinessListing::factory()->create([
            'latitude' => 15.2993,
            'longitude' => 74.1240,
        ]);

        $this->assertEquals(15.2993, $listing->latitude);
        $this->assertEquals(74.1240, $listing->longitude);
    }
}
