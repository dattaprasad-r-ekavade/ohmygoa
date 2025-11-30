<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscription price (₹499/month).
     */
    const MONTHLY_PRICE = 499;

    /**
     * Check if user has active subscription.
     */
    public function hasActiveSubscription(User $user): bool
    {
        return $user->subscription_ends_at && $user->subscription_ends_at->isFuture();
    }

    /**
     * Get subscription status.
     */
    public function getSubscriptionStatus(User $user): array
    {
        return [
            'is_active' => $this->hasActiveSubscription($user),
            'starts_at' => $user->subscription_starts_at,
            'ends_at' => $user->subscription_ends_at,
            'days_remaining' => $this->getDaysRemaining($user),
            'is_expired' => $user->subscription_ends_at && $user->subscription_ends_at->isPast(),
        ];
    }

    /**
     * Get days remaining in subscription.
     */
    public function getDaysRemaining(User $user): ?int
    {
        if (!$user->subscription_ends_at) {
            return null;
        }

        return max(0, now()->diffInDays($user->subscription_ends_at, false));
    }

    /**
     * Create subscription.
     */
    public function createSubscription(User $user, int $months = 1): array
    {
        $amount = self::MONTHLY_PRICE * $months;
        $startsAt = now();
        $endsAt = now()->addMonths($months);

        DB::beginTransaction();
        try {
            // Update user subscription
            $user->update([
                'subscription_starts_at' => $startsAt,
                'subscription_ends_at' => $endsAt,
            ]);

            // Create subscription record
            $subscriptionId = DB::table('subscriptions')->insertGetId([
                'user_id' => $user->id,
                'plan' => 'premium',
                'amount' => $amount,
                'duration_months' => $months,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'action' => 'subscription_created',
                'description' => "Subscribed to premium plan for {$months} month(s)",
                'metadata' => json_encode([
                    'subscription_id' => $subscriptionId,
                    'amount' => $amount,
                    'months' => $months,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'subscription_id' => $subscriptionId,
                'amount' => $amount,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Renew subscription.
     */
    public function renewSubscription(User $user, int $months = 1): array
    {
        $amount = self::MONTHLY_PRICE * $months;
        
        // If subscription is active, extend from end date, otherwise from now
        $startsAt = $this->hasActiveSubscription($user) 
            ? $user->subscription_ends_at 
            : now();
        
        $endsAt = Carbon::parse($startsAt)->addMonths($months);

        DB::beginTransaction();
        try {
            // Update user subscription
            $user->update([
                'subscription_starts_at' => $user->subscription_starts_at ?? now(),
                'subscription_ends_at' => $endsAt,
            ]);

            // Create renewal record
            $subscriptionId = DB::table('subscriptions')->insertGetId([
                'user_id' => $user->id,
                'plan' => 'premium',
                'amount' => $amount,
                'duration_months' => $months,
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'status' => 'active',
                'is_renewal' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'action' => 'subscription_renewed',
                'description' => "Subscription renewed for {$months} month(s)",
                'metadata' => json_encode([
                    'subscription_id' => $subscriptionId,
                    'amount' => $amount,
                    'months' => $months,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return [
                'success' => true,
                'subscription_id' => $subscriptionId,
                'amount' => $amount,
                'ends_at' => $endsAt,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel subscription.
     */
    public function cancelSubscription(User $user): void
    {
        // Mark for cancellation at period end
        DB::table('subscriptions')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'updated_at' => now(),
            ]);

        // Log activity
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'subscription_cancelled',
            'description' => 'Subscription cancelled',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Check and expire subscriptions (run via cron).
     */
    public function checkExpiredSubscriptions(): int
    {
        $expired = User::where('subscription_ends_at', '<=', now())
            ->whereNotNull('subscription_ends_at')
            ->get();

        foreach ($expired as $user) {
            $user->update([
                'subscription_ends_at' => null,
            ]);

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $user->id,
                'action' => 'subscription_expired',
                'description' => 'Subscription expired',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $expired->count();
    }
}
