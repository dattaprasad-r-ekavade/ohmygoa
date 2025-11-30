<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = [
            'basic' => [
                'name' => 'Basic',
                'monthly' => 499,
                'yearly' => 4999,
                'features' => [
                    'Up to 5 business listings',
                    'Basic analytics',
                    'Email support',
                    '1 featured listing per month'
                ]
            ],
            'premium' => [
                'name' => 'Premium',
                'monthly' => 999,
                'yearly' => 9999,
                'features' => [
                    'Unlimited business listings',
                    'Advanced analytics',
                    'Priority support',
                    '5 featured listings per month',
                    'Verified badge',
                    'SEO optimization'
                ]
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'monthly' => 2499,
                'yearly' => 24999,
                'features' => [
                    'Everything in Premium',
                    'Dedicated account manager',
                    'Custom branding',
                    'API access',
                    'White-label options',
                    'Unlimited featured listings'
                ]
            ]
        ];

        $currentSubscription = auth()->check() 
            ? auth()->user()->subscription 
            : null;

        return view('subscriptions.index', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan' => 'required|in:basic,premium,enterprise',
            'billing_cycle' => 'required|in:monthly,yearly'
        ]);

        $user = auth()->user();

        // Check if user already has an active subscription
        $existingSubscription = $user->subscription()
            ->where('status', 'active')
            ->where('ends_at', '>', now())
            ->first();

        if ($existingSubscription) {
            return back()->with('error', 'You already have an active subscription!');
        }

        // Calculate amount based on plan and cycle
        $amounts = [
            'basic' => ['monthly' => 499, 'yearly' => 4999],
            'premium' => ['monthly' => 999, 'yearly' => 9999],
            'enterprise' => ['monthly' => 2499, 'yearly' => 24999]
        ];

        $amount = $amounts[$validated['plan']][$validated['billing_cycle']];

        // Create subscription
        $endsAt = $validated['billing_cycle'] === 'monthly' 
            ? now()->addMonth() 
            : now()->addYear();

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan' => $validated['plan'],
            'amount' => $amount,
            'billing_cycle' => $validated['billing_cycle'],
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'status' => 'active',
            'features' => $this->getPlanFeatures($validated['plan'])
        ]);

        // Create payment record (in real app, integrate with payment gateway)
        Payment::create([
            'user_id' => $user->id,
            'payable_type' => Subscription::class,
            'payable_id' => $subscription->id,
            'amount' => $amount,
            'payment_type' => 'subscription',
            'payment_method' => 'razorpay',
            'status' => 'completed',
            'transaction_id' => 'SUB_' . uniqid()
        ]);

        // Update user role
        $user->update(['role' => 'business']);

        return redirect()->route('subscriptions.success')
            ->with('success', 'Subscription activated successfully!');
    }

    public function cancel()
    {
        $user = auth()->user();
        $subscription = $user->subscription()
            ->where('status', 'active')
            ->firstOrFail();

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return back()->with('success', 'Subscription cancelled successfully!');
    }

    public function renew($id)
    {
        $subscription = Subscription::where('user_id', auth()->id())
            ->findOrFail($id);

        if ($subscription->status === 'active' && $subscription->ends_at > now()) {
            return back()->with('error', 'Subscription is still active!');
        }

        // Renew subscription
        $endsAt = $subscription->billing_cycle === 'monthly' 
            ? now()->addMonth() 
            : now()->addYear();

        $subscription->update([
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'status' => 'active',
            'cancelled_at' => null
        ]);

        // Create payment
        Payment::create([
            'user_id' => $subscription->user_id,
            'payable_type' => Subscription::class,
            'payable_id' => $subscription->id,
            'amount' => $subscription->amount,
            'payment_type' => 'subscription_renewal',
            'payment_method' => 'razorpay',
            'status' => 'completed',
            'transaction_id' => 'REN_' . uniqid()
        ]);

        return back()->with('success', 'Subscription renewed successfully!');
    }

    public function upgrade(Request $request)
    {
        $validated = $request->validate([
            'new_plan' => 'required|in:basic,premium,enterprise'
        ]);

        $user = auth()->user();
        $currentSubscription = $user->subscription()
            ->where('status', 'active')
            ->firstOrFail();

        // Calculate prorated amount
        $amounts = [
            'basic' => ['monthly' => 499, 'yearly' => 4999],
            'premium' => ['monthly' => 999, 'yearly' => 9999],
            'enterprise' => ['monthly' => 2499, 'yearly' => 24999]
        ];

        $newAmount = $amounts[$validated['new_plan']][$currentSubscription->billing_cycle];

        // Update subscription
        $currentSubscription->update([
            'plan' => $validated['new_plan'],
            'amount' => $newAmount,
            'features' => $this->getPlanFeatures($validated['new_plan'])
        ]);

        return back()->with('success', 'Subscription upgraded successfully!');
    }

    private function getPlanFeatures($plan)
    {
        $features = [
            'basic' => [
                'max_listings' => 5,
                'featured_listings_per_month' => 1,
                'analytics' => 'basic',
                'support' => 'email',
                'verified_badge' => false
            ],
            'premium' => [
                'max_listings' => -1, // unlimited
                'featured_listings_per_month' => 5,
                'analytics' => 'advanced',
                'support' => 'priority',
                'verified_badge' => true,
                'seo_optimization' => true
            ],
            'enterprise' => [
                'max_listings' => -1,
                'featured_listings_per_month' => -1,
                'analytics' => 'advanced',
                'support' => 'dedicated',
                'verified_badge' => true,
                'seo_optimization' => true,
                'api_access' => true,
                'white_label' => true
            ]
        ];

        return $features[$plan] ?? [];
    }
}
