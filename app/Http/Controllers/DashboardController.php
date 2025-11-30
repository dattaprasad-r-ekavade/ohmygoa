<?php

namespace App\Http\Controllers;

use App\Models\BusinessListing;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Product;
use App\Models\Classified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Common stats for all users
        $commonStats = [
            'points_balance' => $user->points_balance,
            'unread_notifications' => $user->notifications()->unread()->count(),
            'total_bookmarks' => $user->bookmarks()->count(),
            'total_reviews' => $user->reviews()->count(),
        ];

        // Role-specific stats and data
        if ($user->role === 'business' || $user->role === 'admin') {
            $businessStats = [
                'total_listings' => $user->listings()->count(),
                'active_listings' => $user->listings()->where('status', 'approved')->where('is_active', true)->count(),
                'total_events' => $user->events()->count(),
                'total_jobs' => $user->jobListings()->count(),
                'total_products' => $user->products()->count(),
                'total_views' => $user->listings()->sum('views_count'),
                'total_enquiries' => $user->receivedEnquiries()->count(),
                'pending_approval' => $user->listings()->where('status', 'pending')->count(),
            ];

            // Recent listings
            $recentListings = $user->listings()
                ->with(['category', 'location'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Recent enquiries
            $recentEnquiries = $user->receivedEnquiries()
                ->with(['user', 'enquirable'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Views chart data (last 30 days)
            $viewsChart = $user->listings()
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(views_count) as views'))
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('views', 'date');

            // Top performing listings
            $topListings = $user->listings()
                ->where('status', 'approved')
                ->orderBy('views_count', 'desc')
                ->take(5)
                ->get();

            return view('dashboard', array_merge($commonStats, $businessStats, [
                'recentListings' => $recentListings,
                'recentEnquiries' => $recentEnquiries,
                'viewsChart' => $viewsChart,
                'topListings' => $topListings,
            ]));

        } else {
            // Free user stats
            $freeUserStats = [
                'job_applications' => $user->jobApplications()->count(),
                'service_bookings' => $user->serviceBookings()->count(),
                'classifieds_posted' => $user->classifieds()->count(),
                'coupons_redeemed' => $user->couponRedemptions()->count(),
            ];

            // Recent bookmarks
            $recentBookmarks = $user->bookmarks()
                ->with('bookmarkable')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Recent reviews
            $recentReviews = $user->reviews()
                ->with('reviewable')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            // Recent job applications
            $recentApplications = $user->jobApplications()
                ->with('jobListing')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            return view('dashboard', array_merge($commonStats, $freeUserStats, [
                'recentBookmarks' => $recentBookmarks,
                'recentReviews' => $recentReviews,
                'recentApplications' => $recentApplications,
            ]));
        }
    }
}
