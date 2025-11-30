<?php

namespace Tests\Feature;

use App\Models\BusinessListing;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BusinessListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_can_view_approved_business_listings()
    {
        $listing = BusinessListing::factory()->create([
            'status' => 'approved',
            'business_name' => 'Test Restaurant',
        ]);

        $response = $this->get('/listings');

        $response->assertStatus(200);
        $response->assertSee('Test Restaurant');
    }

    /** @test */
    public function guests_cannot_view_pending_business_listings()
    {
        $listing = BusinessListing::factory()->create([
            'status' => 'pending',
            'business_name' => 'Pending Business',
        ]);

        $response = $this->get('/listings');

        $response->assertStatus(200);
        $response->assertDontSee('Pending Business');
    }

    /** @test */
    public function guests_can_view_single_approved_listing()
    {
        $listing = BusinessListing::factory()->create([
            'status' => 'approved',
            'slug' => 'test-listing',
        ]);

        $response = $this->get("/listings/{$listing->slug}");

        $response->assertStatus(200);
        $response->assertSee($listing->business_name);
    }

    /** @test */
    public function business_users_can_create_listings()
    {
        $user = User::factory()->create(['role' => 'business']);
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $response = $this->actingAs($user)->post('/listings', [
            'business_name' => 'New Restaurant',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'description' => 'A great restaurant in Goa',
            'contact_phone' => '9876543210',
            'contact_email' => 'restaurant@example.com',
            'address' => '123 Beach Road, Calangute',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('business_listings', [
            'business_name' => 'New Restaurant',
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function free_users_cannot_create_listings()
    {
        $user = User::factory()->create(['role' => 'free']);
        $category = Category::factory()->create();
        $location = Location::factory()->create();

        $response = $this->actingAs($user)->post('/listings', [
            'business_name' => 'New Restaurant',
            'category_id' => $category->id,
            'location_id' => $location->id,
            'description' => 'A great restaurant in Goa',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function listing_creation_requires_business_name()
    {
        $user = User::factory()->create(['role' => 'business']);

        $response = $this->actingAs($user)->post('/listings', [
            'category_id' => 1,
            'location_id' => 1,
            'description' => 'Description',
        ]);

        $response->assertSessionHasErrors('business_name');
    }

    /** @test */
    public function listing_creation_requires_category()
    {
        $user = User::factory()->create(['role' => 'business']);

        $response = $this->actingAs($user)->post('/listings', [
            'business_name' => 'Test Business',
            'location_id' => 1,
            'description' => 'Description',
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    /** @test */
    public function business_owner_can_update_their_listing()
    {
        $user = User::factory()->create(['role' => 'business']);
        $listing = BusinessListing::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/listings/{$listing->id}", [
            'business_name' => 'Updated Business Name',
            'category_id' => $listing->category_id,
            'location_id' => $listing->location_id,
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('business_listings', [
            'id' => $listing->id,
            'business_name' => 'Updated Business Name',
        ]);
    }

    /** @test */
    public function users_cannot_update_others_listings()
    {
        $owner = User::factory()->create(['role' => 'business']);
        $otherUser = User::factory()->create(['role' => 'business']);
        $listing = BusinessListing::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->put("/listings/{$listing->id}", [
            'business_name' => 'Hacked Name',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function business_owner_can_delete_their_listing()
    {
        $user = User::factory()->create(['role' => 'business']);
        $listing = BusinessListing::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/listings/{$listing->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('business_listings', ['id' => $listing->id]);
    }

    /** @test */
    public function listing_increments_view_count_on_visit()
    {
        $listing = BusinessListing::factory()->create([
            'status' => 'approved',
            'view_count' => 0,
        ]);

        $this->get("/listings/{$listing->slug}");

        $this->assertEquals(1, $listing->fresh()->view_count);
    }

    /** @test */
    public function listings_can_be_filtered_by_category()
    {
        $category = Category::factory()->create(['slug' => 'restaurants']);
        
        $listing1 = BusinessListing::factory()->create([
            'category_id' => $category->id,
            'status' => 'approved',
        ]);

        $listing2 = BusinessListing::factory()->create([
            'status' => 'approved',
        ]);

        $response = $this->get("/listings?category={$category->slug}");

        $response->assertStatus(200);
        $response->assertSee($listing1->business_name);
    }

    /** @test */
    public function listings_can_be_filtered_by_location()
    {
        $location = Location::factory()->create(['slug' => 'panaji']);
        
        $listing1 = BusinessListing::factory()->create([
            'location_id' => $location->id,
            'status' => 'approved',
        ]);

        $response = $this->get("/listings?location={$location->slug}");

        $response->assertStatus(200);
        $response->assertSee($listing1->business_name);
    }
}
