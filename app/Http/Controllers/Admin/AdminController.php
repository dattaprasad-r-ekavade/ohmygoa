<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessListing;
use App\Models\Classified;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ServiceExpert;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with analytics.
     */
    public function dashboard(): View
    {
        // User statistics
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeUsers = User::where('is_active', true)->count();
        $businessUsers = User::where('role', 'business')->count();

        // Content statistics
        $totalListings = BusinessListing::count();
        $pendingListings = BusinessListing::where('status', 'pending')->count();
        $activeListings = BusinessListing::where('status', 'active')->count();

        $totalProducts = Product::count();
        $pendingProducts = Product::where('status', 'pending')->count();

        $totalClassifieds = Classified::count();
        $pendingClassifieds = Classified::where('status', 'pending')->count();

        $totalServiceExperts = ServiceExpert::count();
        $pendingServiceExperts = ServiceExpert::where('status', 'pending')->count();

        $totalJobs = JobListing::count();
        $activeJobs = JobListing::where('status', 'active')->count();

        $totalEvents = Event::count();
        $upcomingEvents = Event::where('start_date', '>', now())->count();

        // Financial statistics
        $totalRevenue = Payment::where('payment_status', 'completed')->sum('amount');
        $revenueThisMonth = Payment::where('payment_status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // Recent activities
        $recentListings = BusinessListing::with('user')
            ->latest()
            ->limit(5)
            ->get();

        $recentUsers = User::latest()
            ->limit(5)
            ->get();

        $recentPayments = Payment::with('user')
            ->where('payment_status', 'completed')
            ->latest()
            ->limit(5)
            ->get();

        // Content pending approval counts
        $pendingApprovals = [
            'listings' => $pendingListings,
            'products' => $pendingProducts,
            'classifieds' => $pendingClassifieds,
            'service_experts' => $pendingServiceExperts,
            'total' => $pendingListings + $pendingProducts + $pendingClassifieds + $pendingServiceExperts,
        ];

        // Monthly user registrations chart data (last 12 months)
        $userRegistrations = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Monthly revenue chart data (last 12 months)
        $monthlyRevenue = Payment::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Top categories by listings
        $topCategories = DB::table('business_listings')
            ->join('categories', 'business_listings.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Top locations by listings
        $topLocations = DB::table('business_listings')
            ->join('locations', 'business_listings.location_id', '=', 'locations.id')
            ->select('locations.name', DB::raw('COUNT(*) as count'))
            ->groupBy('locations.id', 'locations.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsersThisMonth',
            'activeUsers',
            'businessUsers',
            'totalListings',
            'pendingListings',
            'activeListings',
            'totalProducts',
            'pendingProducts',
            'totalClassifieds',
            'pendingClassifieds',
            'totalServiceExperts',
            'pendingServiceExperts',
            'totalJobs',
            'activeJobs',
            'totalEvents',
            'upcomingEvents',
            'totalRevenue',
            'revenueThisMonth',
            'recentListings',
            'recentUsers',
            'recentPayments',
            'pendingApprovals',
            'userRegistrations',
            'monthlyRevenue',
            'topCategories',
            'topLocations'
        ));
    }
}
