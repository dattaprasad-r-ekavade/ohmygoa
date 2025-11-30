<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentModerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_pending_listings()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        $pending = BusinessListing::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->get('/admin/content/pending');

        $response->assertStatus(200);
        $response->assertSee($pending->business_name);
    }

    /** @test */
    public function non_admin_cannot_view_pending_listings()
    {
        $user = User::factory()->create(['role' => 'business']);

        $response = $this->actingAs($user)->get('/admin/content/pending');

        $response->assertForbidden();
    }

    /** @test */
    public function admin_can_approve_listing()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $listing = BusinessListing::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->post("/admin/listings/{$listing->id}/approve");

        $response->assertRedirect();
        $this->assertDatabaseHas('business_listings', [
            'id' => $listing->id,
            'status' => 'approved',
        ]);
    }

    /** @test */
    public function admin_can_reject_listing()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $listing = BusinessListing::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->post("/admin/listings/{$listing->id}/reject", [
            'reason' => 'Inappropriate content',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('business_listings', [
            'id' => $listing->id,
            'status' => 'rejected',
        ]);
    }

    /** @test */
    public function business_owner_is_notified_on_approval()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $businessOwner = User::factory()->create(['role' => 'business']);
        $listing = BusinessListing::factory()->create([
            'user_id' => $businessOwner->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/listings/{$listing->id}/approve");

        // Check notification was created
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $businessOwner->id,
            'type' => 'App\Notifications\ListingApproved',
        ]);
    }

    /** @test */
    public function admin_can_view_all_users()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(10)->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_suspend_user()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->post("/admin/users/{$user->id}/suspend");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    /** @test */
    public function admin_can_view_statistics()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/statistics');

        $response->assertStatus(200);
        $response->assertViewHas(['totalUsers', 'totalListings', 'revenue']);
    }
}
