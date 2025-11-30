<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\BusinessListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_business_listings()
    {
        $location = Location::factory()->create();
        
        BusinessListing::factory()->count(4)->create(['location_id' => $location->id]);

        $this->assertCount(4, $location->businessListings);
        $this->assertInstanceOf(BusinessListing::class, $location->businessListings->first());
    }

    /** @test */
    public function it_generates_unique_slug()
    {
        $location1 = Location::factory()->create(['name' => 'Panaji']);
        $location2 = Location::factory()->create(['name' => 'Panaji']);

        $this->assertNotEquals($location1->slug, $location2->slug);
        $this->assertStringContainsString('panaji', $location1->slug);
    }

    /** @test */
    public function it_has_state_attribute()
    {
        $location = Location::factory()->create(['state' => 'Goa']);

        $this->assertEquals('Goa', $location->state);
    }

    /** @test */
    public function it_has_country_attribute()
    {
        $location = Location::factory()->create(['country' => 'India']);

        $this->assertEquals('India', $location->country);
    }

    /** @test */
    public function it_can_be_active_or_inactive()
    {
        $activeLocation = Location::factory()->create(['is_active' => true]);
        $inactiveLocation = Location::factory()->create(['is_active' => false]);

        $this->assertTrue($activeLocation->is_active);
        $this->assertFalse($inactiveLocation->is_active);
    }

    /** @test */
    public function it_can_be_popular()
    {
        $popularLocation = Location::factory()->create(['is_popular' => true]);
        $regularLocation = Location::factory()->create(['is_popular' => false]);

        $this->assertTrue($popularLocation->is_popular);
        $this->assertFalse($regularLocation->is_popular);
    }

    /** @test */
    public function it_has_latitude_and_longitude()
    {
        $location = Location::factory()->create([
            'latitude' => 15.2993,
            'longitude' => 74.1240,
        ]);

        $this->assertEquals(15.2993, $location->latitude);
        $this->assertEquals(74.1240, $location->longitude);
    }

    /** @test */
    public function it_has_display_order()
    {
        $location1 = Location::factory()->create(['display_order' => 1]);
        $location2 = Location::factory()->create(['display_order' => 2]);

        $this->assertEquals(1, $location1->display_order);
        $this->assertEquals(2, $location2->display_order);
    }

    /** @test */
    public function it_can_have_description()
    {
        $description = 'Capital city of Goa';
        $location = Location::factory()->create(['description' => $description]);

        $this->assertEquals($description, $location->description);
    }

    /** @test */
    public function it_formats_coordinates_correctly()
    {
        $location = Location::factory()->create([
            'latitude' => 15.299326,
            'longitude' => 74.123996,
        ]);

        $this->assertIsFloat($location->latitude);
        $this->assertIsFloat($location->longitude);
        $this->assertEquals(15.299326, $location->latitude);
        $this->assertEquals(74.123996, $location->longitude);
    }

    /** @test */
    public function it_counts_listings_by_location()
    {
        $location = Location::factory()->create();
        
        BusinessListing::factory()->count(6)->create([
            'location_id' => $location->id,
            'status' => 'approved',
        ]);

        BusinessListing::factory()->count(3)->create([
            'location_id' => $location->id,
            'status' => 'pending',
        ]);

        $this->assertCount(9, $location->businessListings);
        $this->assertEquals(6, $location->businessListings()->where('status', 'approved')->count());
    }
}
