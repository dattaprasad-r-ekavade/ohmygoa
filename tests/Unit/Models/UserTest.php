<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\BusinessListing;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $fillable = [
            'name', 'email', 'password', 'phone', 'role', 'status',
            'email_verified_at', 'wallet_balance', 'total_points',
            'profile_picture', 'bio', 'city', 'state', 'country',
            'subscription_plan', 'subscription_status', 'subscription_start',
            'subscription_end', 'last_login_at'
        ];

        $user = new User();
        $this->assertEquals($fillable, $user->getFillable());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'subscription_start' => now(),
            'subscription_end' => now()->addMonth(),
            'last_login_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->subscription_start);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->subscription_end);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->last_login_at);
    }

    /** @test */
    public function it_hides_password_attribute()
    {
        $user = User::factory()->create(['password' => 'secret']);
        
        $array = $user->toArray();
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function it_checks_if_user_is_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $business = User::factory()->create(['role' => 'business']);
        $free = User::factory()->create(['role' => 'free']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($business->isAdmin());
        $this->assertFalse($free->isAdmin());
    }

    /** @test */
    public function it_checks_if_user_is_business()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $business = User::factory()->create(['role' => 'business']);
        $free = User::factory()->create(['role' => 'free']);

        $this->assertFalse($admin->isBusiness());
        $this->assertTrue($business->isBusiness());
        $this->assertFalse($free->isBusiness());
    }

    /** @test */
    public function it_checks_if_user_has_active_subscription()
    {
        $activeUser = User::factory()->create([
            'role' => 'business',
            'subscription_status' => 'active',
            'subscription_end' => now()->addMonth(),
        ]);

        $expiredUser = User::factory()->create([
            'role' => 'business',
            'subscription_status' => 'active',
            'subscription_end' => now()->subDay(),
        ]);

        $inactiveUser = User::factory()->create([
            'role' => 'business',
            'subscription_status' => 'inactive',
        ]);

        $this->assertTrue($activeUser->hasActiveSubscription());
        $this->assertFalse($expiredUser->hasActiveSubscription());
        $this->assertFalse($inactiveUser->hasActiveSubscription());
    }

    /** @test */
    public function it_calculates_wallet_balance_correctly()
    {
        $user = User::factory()->create(['wallet_balance' => 1500.50]);
        
        $this->assertEquals(1500.50, $user->wallet_balance);
        
        $user->increment('wallet_balance', 500);
        $this->assertEquals(2000.50, $user->fresh()->wallet_balance);
        
        $user->decrement('wallet_balance', 200);
        $this->assertEquals(1800.50, $user->fresh()->wallet_balance);
    }

    /** @test */
    public function it_has_many_business_listings()
    {
        $user = User::factory()->create(['role' => 'business']);
        
        BusinessListing::factory()->count(3)->create(['user_id' => $user->id]);
        
        $this->assertCount(3, $user->businessListings);
        $this->assertInstanceOf(BusinessListing::class, $user->businessListings->first());
    }

    /** @test */
    public function it_has_many_reviews()
    {
        $user = User::factory()->create();
        $listing = BusinessListing::factory()->create();
        
        Review::factory()->count(2)->create([
            'user_id' => $user->id,
            'reviewable_type' => BusinessListing::class,
            'reviewable_id' => $listing->id,
        ]);
        
        $this->assertCount(2, $user->reviews);
        $this->assertInstanceOf(Review::class, $user->reviews->first());
    }

    /** @test */
    public function it_can_add_points()
    {
        $user = User::factory()->create(['total_points' => 100]);
        
        $user->increment('total_points', 50);
        
        $this->assertEquals(150, $user->fresh()->total_points);
    }

    /** @test */
    public function it_can_deduct_points()
    {
        $user = User::factory()->create(['total_points' => 1000]);
        
        $user->decrement('total_points', 200);
        
        $this->assertEquals(800, $user->fresh()->total_points);
    }

    /** @test */
    public function it_formats_subscription_dates()
    {
        $user = User::factory()->create([
            'subscription_start' => '2025-01-01',
            'subscription_end' => '2025-02-01',
        ]);

        $this->assertEquals('2025-01-01', $user->subscription_start->format('Y-m-d'));
        $this->assertEquals('2025-02-01', $user->subscription_end->format('Y-m-d'));
    }

    /** @test */
    public function it_can_check_if_subscription_is_expiring_soon()
    {
        $expiringSoon = User::factory()->create([
            'subscription_status' => 'active',
            'subscription_end' => now()->addDays(5),
        ]);

        $notExpiringSoon = User::factory()->create([
            'subscription_status' => 'active',
            'subscription_end' => now()->addDays(20),
        ]);

        $daysUntilExpiry = $expiringSoon->subscription_end->diffInDays(now());
        $this->assertLessThan(7, $daysUntilExpiry);
        
        $daysUntilExpiry2 = $notExpiringSoon->subscription_end->diffInDays(now());
        $this->assertGreaterThan(7, $daysUntilExpiry2);
    }
}
