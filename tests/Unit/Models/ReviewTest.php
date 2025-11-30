<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use App\Models\User;
use App\Models\BusinessListing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create();
        
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertEquals($user->id, $review->user->id);
    }

    /** @test */
    public function it_belongs_to_reviewable_polymorphic()
    {
        $listing = BusinessListing::factory()->create();
        
        $review = Review::factory()->create([
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);

        $this->assertInstanceOf(BusinessListing::class, $review->reviewable);
        $this->assertEquals($listing->id, $review->reviewable->id);
    }

    /** @test */
    public function it_validates_rating_is_between_1_and_5()
    {
        $review = Review::factory()->create(['rating' => 3]);
        
        $this->assertGreaterThanOrEqual(1, $review->rating);
        $this->assertLessThanOrEqual(5, $review->rating);
    }

    /** @test */
    public function it_can_be_approved()
    {
        $review = Review::factory()->create(['status' => 'pending']);

        $this->assertEquals('pending', $review->status);
        
        $review->update(['status' => 'approved']);
        
        $this->assertEquals('approved', $review->fresh()->status);
    }

    /** @test */
    public function it_can_be_rejected()
    {
        $review = Review::factory()->create(['status' => 'pending']);

        $review->update(['status' => 'rejected', 'rejection_reason' => 'Inappropriate content']);
        
        $this->assertEquals('rejected', $review->fresh()->status);
        $this->assertEquals('Inappropriate content', $review->fresh()->rejection_reason);
    }

    /** @test */
    public function it_tracks_helpful_count()
    {
        $review = Review::factory()->create(['helpful_count' => 0]);

        $review->increment('helpful_count');
        $this->assertEquals(1, $review->fresh()->helpful_count);

        $review->increment('helpful_count', 3);
        $this->assertEquals(4, $review->fresh()->helpful_count);
    }

    /** @test */
    public function it_can_have_photos()
    {
        $photos = ['photo1.jpg', 'photo2.jpg', 'photo3.jpg'];
        
        $review = Review::factory()->create(['photos' => $photos]);

        $this->assertEquals($photos, $review->photos);
        $this->assertIsArray($review->photos);
        $this->assertCount(3, $review->photos);
    }

    /** @test */
    public function it_stores_review_text()
    {
        $reviewText = 'This is an excellent business! Highly recommended.';
        
        $review = Review::factory()->create(['review' => $reviewText]);

        $this->assertEquals($reviewText, $review->review);
    }

    /** @test */
    public function it_stores_review_title()
    {
        $review = Review::factory()->create([
            'title' => 'Great Service',
            'review' => 'Excellent experience overall.',
        ]);

        $this->assertEquals('Great Service', $review->title);
    }

    /** @test */
    public function it_formats_created_date()
    {
        $review = Review::factory()->create([
            'created_at' => '2025-01-15 10:30:00',
        ]);

        $this->assertEquals('2025-01-15', $review->created_at->format('Y-m-d'));
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $review->created_at);
    }
}
