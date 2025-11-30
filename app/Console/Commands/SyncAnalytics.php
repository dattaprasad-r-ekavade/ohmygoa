<?php

namespace App\Console\Commands;

use App\Models\BusinessListing;
use App\Models\Classified;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync daily analytics and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing analytics for ' . now()->toDateString());

        $analytics = [
            'date' => now()->toDateString(),
            
            // User stats
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'active_users' => User::where('is_active', true)->count(),
            'premium_users' => User::where('is_premium', true)->count(),
            
            // Business listings
            'total_listings' => BusinessListing::count(),
            'active_listings' => BusinessListing::where('status', 'approved')->count(),
            'pending_listings' => BusinessListing::where('status', 'pending')->count(),
            'listings_today' => BusinessListing::whereDate('created_at', today())->count(),
            
            // Events
            'total_events' => Event::count(),
            'active_events' => Event::where('status', 'approved')->count(),
            'upcoming_events' => Event::where('status', 'approved')
                ->where('start_date', '>=', today())
                ->count(),
            
            // Jobs
            'total_jobs' => JobListing::count(),
            'active_jobs' => JobListing::where('status', 'active')->count(),
            'jobs_today' => JobListing::whereDate('created_at', today())->count(),
            
            // Products
            'total_products' => Product::count(),
            'active_products' => Product::where('status', 'approved')->count(),
            'products_today' => Product::whereDate('created_at', today())->count(),
            
            // Coupons
            'total_coupons' => Coupon::count(),
            'active_coupons' => Coupon::where('status', 'approved')
                ->where('valid_until', '>', now())
                ->count(),
            'coupons_sold_today' => DB::table('redemptions')
                ->whereDate('created_at', today())
                ->count(),
            
            // Classifieds
            'total_classifieds' => Classified::count(),
            'active_classifieds' => Classified::where('status', 'approved')
                ->where('expires_at', '>', now())
                ->count(),
            
            // Revenue stats
            'revenue_today' => DB::table('payments')
                ->where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
            
            'subscriptions_today' => DB::table('subscriptions')
                ->whereDate('created_at', today())
                ->count(),
            
            // Engagement
            'total_reviews' => DB::table('reviews')->count(),
            'reviews_today' => DB::table('reviews')
                ->whereDate('created_at', today())
                ->count(),
            
            'total_bookmarks' => DB::table('bookmarks')->count(),
            'total_follows' => DB::table('follows')->count(),
        ];

        // Store in analytics table (if exists) or log
        DB::table('activity_logs')->insert([
            'user_id' => null,
            'activity_type' => 'daily_analytics',
            'description' => json_encode($analytics),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info('Analytics synced successfully:');
        $this->table(
            ['Metric', 'Value'],
            collect($analytics)->map(fn($value, $key) => [$key, $value])->toArray()
        );

        return 0;
    }
}
