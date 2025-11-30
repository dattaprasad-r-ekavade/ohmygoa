<?php

namespace Tests\Feature;

use App\Models\BusinessListing;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function search_page_can_be_rendered()
    {
        $response = $this->get('/search');

        $response->assertStatus(200);
    }

    /** @test */
    public function users_can_search_for_business_listings()
    {
        $listing1 = BusinessListing::factory()->create([
            'business_name' => 'Taj Vivanta Hotel',
            'status' => 'approved',
        ]);

        $listing2 = BusinessListing::factory()->create([
            'business_name' => 'Beach Shack Restaurant',
            'status' => 'approved',
        ]);

        $response = $this->get('/search?q=Hotel');

        $response->assertStatus(200);
        $response->assertSee('Taj Vivanta Hotel');
        $response->assertDontSee('Beach Shack Restaurant');
    }

    /** @test */
    public function search_returns_results_from_description()
    {
        $listing = BusinessListing::factory()->create([
            'business_name' => 'XYZ Business',
            'description' => 'Best seafood restaurant in Goa with ocean view',
            'status' => 'approved',
        ]);

        $response = $this->get('/search?q=seafood');

        $response->assertStatus(200);
        $response->assertSee('XYZ Business');
    }

    /** @test */
    public function search_can_be_filtered_by_category()
    {
        $restaurantCategory = Category::factory()->create([
            'name' => 'Restaurants',
            'slug' => 'restaurants',
        ]);

        $hotelCategory = Category::factory()->create([
            'name' => 'Hotels',
            'slug' => 'hotels',
        ]);

        $restaurant = BusinessListing::factory()->create([
            'business_name' => 'Great Restaurant',
            'category_id' => $restaurantCategory->id,
            'status' => 'approved',
        ]);

        $hotel = BusinessListing::factory()->create([
            'business_name' => 'Great Hotel',
            'category_id' => $hotelCategory->id,
            'status' => 'approved',
        ]);

        $response = $this->get("/search?q=Great&category={$restaurantCategory->id}");

        $response->assertStatus(200);
        $response->assertSee('Great Restaurant');
        $response->assertDontSee('Great Hotel');
    }

    /** @test */
    public function search_can_be_filtered_by_location()
    {
        $panaji = Location::factory()->create([
            'name' => 'Panaji',
            'slug' => 'panaji',
        ]);

        $calangute = Location::factory()->create([
            'name' => 'Calangute',
            'slug' => 'calangute',
        ]);

        $listing1 = BusinessListing::factory()->create([
            'business_name' => 'Test Business',
            'location_id' => $panaji->id,
            'status' => 'approved',
        ]);

        $listing2 = BusinessListing::factory()->create([
            'business_name' => 'Another Business',
            'location_id' => $calangute->id,
            'status' => 'approved',
        ]);

        $response = $this->get("/search?location={$panaji->id}");

        $response->assertStatus(200);
        $response->assertSee('Test Business');
        $response->assertDontSee('Another Business');
    }

    /** @test */
    public function search_results_are_paginated()
    {
        BusinessListing::factory()->count(25)->create([
            'status' => 'approved',
            'business_name' => 'Test Business',
        ]);

        $response = $this->get('/search?q=Test');

        $response->assertStatus(200);
        $response->assertViewHas('listings');
    }

    /** @test */
    public function empty_search_returns_all_approved_listings()
    {
        BusinessListing::factory()->count(5)->create(['status' => 'approved']);

        $response = $this->get('/search');

        $response->assertStatus(200);
    }

    /** @test */
    public function search_only_returns_approved_listings()
    {
        $approved = BusinessListing::factory()->create([
            'business_name' => 'Approved Business',
            'status' => 'approved',
        ]);

        $pending = BusinessListing::factory()->create([
            'business_name' => 'Pending Business',
            'status' => 'pending',
        ]);

        $rejected = BusinessListing::factory()->create([
            'business_name' => 'Rejected Business',
            'status' => 'rejected',
        ]);

        $response = $this->get('/search?q=Business');

        $response->assertStatus(200);
        $response->assertSee('Approved Business');
        $response->assertDontSee('Pending Business');
        $response->assertDontSee('Rejected Business');
    }

    /** @test */
    public function search_is_case_insensitive()
    {
        $listing = BusinessListing::factory()->create([
            'business_name' => 'Great Restaurant',
            'status' => 'approved',
        ]);

        $response = $this->get('/search?q=GREAT');

        $response->assertStatus(200);
        $response->assertSee('Great Restaurant');
    }

    /** @test */
    public function search_handles_special_characters()
    {
        $listing = BusinessListing::factory()->create([
            'business_name' => "Joe's Café & Restaurant",
            'status' => 'approved',
        ]);

        $response = $this->get('/search?q=Café');

        $response->assertStatus(200);
    }
}
