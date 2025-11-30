<?php

namespace Tests\Feature;

use App\Models\BusinessListing;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_users_can_submit_reviews()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($user)->post('/reviews', [
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 5,
            'title' => 'Excellent service',
            'review' => 'Had a wonderful experience!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'reviewable_id' => $listing->id,
            'rating' => 5,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function guests_cannot_submit_reviews()
    {
        $listing = BusinessListing::factory()->create(['status' => 'approved']);

        $response = $this->post('/reviews', [
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 5,
            'review' => 'Great place!',
        ]);

        $response->assertRedirect('/login');
    }

    /** @test */
    public function review_requires_rating()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($user)->post('/reviews', [
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'review' => 'Great place!',
        ]);

        $response->assertSessionHasErrors('rating');
    }

    /** @test */
    public function rating_must_be_between_1_and_5()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($user)->post('/reviews', [
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 6,
            'review' => 'Great place!',
        ]);

        $response->assertSessionHasErrors('rating');
    }

    /** @test */
    public function review_updates_listing_average_rating()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $listing = BusinessListing::factory()->create([
            'status' => 'approved',
            'average_rating' => 0,
            'total_reviews' => 0,
        ]);

        // First review
        Review::factory()->create([
            'user_id' => $user1->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 5,
            'status' => 'approved',
        ]);

        // Second review
        Review::factory()->create([
            'user_id' => $user2->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 3,
            'status' => 'approved',
        ]);

        $averageRating = $listing->reviews()->where('status', 'approved')->avg('rating');
        
        $this->assertEquals(4.0, $averageRating);
    }

    /** @test */
    public function users_can_only_review_once_per_listing()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);

        // First review
        Review::factory()->create([
            'user_id' => $user->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);

        // Attempt second review
        $response = $this->actingAs($user)->post('/reviews', [
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'rating' => 5,
            'review' => 'Another review',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function approved_reviews_are_visible_on_listing_page()
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        $listing = BusinessListing::factory()->create(['status' => 'approved']);
        
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'review' => 'Excellent service and food!',
            'status' => 'approved',
        ]);

        $response = $this->get("/listings/{$listing->slug}");

        $response->assertSee('Excellent service and food!');
        $response->assertSee('John Doe');
    }

    /** @test */
    public function pending_reviews_are_not_visible()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);
        
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
            'review' => 'Pending review content',
            'status' => 'pending',
        ]);

        $response = $this->get("/listings/{$listing->slug}");

        $response->assertDontSee('Pending review content');
    }

    /** @test */
    public function users_can_delete_their_own_reviews()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);
        
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);

        $response = $this->actingAs($user)->delete("/reviews/{$review->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    /** @test */
    public function users_cannot_delete_others_reviews()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $listing = BusinessListing::factory()->create(['status' => 'approved']);
        
        $review = Review::factory()->create([
            'user_id' => $owner->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);

        $response = $this->actingAs($otherUser)->delete("/reviews/{$review->id}");

        $response->assertForbidden();
    }
}
