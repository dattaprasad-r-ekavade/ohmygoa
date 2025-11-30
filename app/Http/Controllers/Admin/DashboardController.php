<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\Job;
use App\Models\Product;
use App\Models\Event;
use App\Models\Classified;
use App\Models\ServiceExpert;
use App\Models\News;
use App\Models\Place;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Enquiry;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // User statistics
        $totalUsers = User::count();
        $freeUsers = User::where('role', 'free')->count();
        $businessUsers = User::where('role', 'business')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // Active subscriptions
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        // Content statistics
        $totalListings = Listing::count();
        $activeListings = Listing::where('status', 'active')->count();
        $pendingListings = Listing::where('status', 'pending')->count();

        $totalJobs = Job::count();
        $activeJobs = Job::where('status', 'active')->where('is_active', true)->count();
        $pendingJobs = Job::where('status', 'pending')->count();

        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'active')->where('is_active', true)->count();

        $totalEvents = Event::count();
        $upcomingEvents = Event::where('status', 'active')
            ->where('start_date', '>', now())
            ->count();

        $totalClassifieds = Classified::count();
        $activeClassifieds = Classified::where('status', 'active')->count();

        $totalServiceExperts = ServiceExpert::count();
        $verifiedExperts = ServiceExpert::where('is_verified', true)->count();

        $totalNews = News::count();
        $publishedNews = News::where('status', 'published')->count();
        $pendingNews = News::where('status', 'pending')->count();

        $totalPlaces = Place::count();
        $activePlaces = Place::where('status', 'active')->count();

        // Financial statistics
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        // Enquiries
        $totalEnquiries = Enquiry::count();
        $newEnquiries = Enquiry::where('status', 'new')->count();
        $todayEnquiries = Enquiry::whereDate('created_at', today())->count();

        // Recent activity
        $recentUsers = User::latest()->limit(5)->get();
        $recentListings = Listing::with('user')->latest()->limit(5)->get();
        $recentPayments = Payment::with('user')->latest()->limit(5)->get();
        $recentEnquiries = Enquiry::with(['user', 'enquirable'])->latest()->limit(5)->get();

        // Charts data - User registrations (last 30 days)
        $userRegistrations = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue chart (last 12 months)
        $revenueData = Payment::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'freeUsers', 'businessUsers', 'newUsersToday', 'newUsersThisMonth',
            'activeSubscriptions', 'expiringSubscriptions',
            'totalListings', 'activeListings', 'pendingListings',
            'totalJobs', 'activeJobs', 'pendingJobs',
            'totalProducts', 'activeProducts',
            'totalEvents', 'upcomingEvents',
            'totalClassifieds', 'activeClassifieds',
            'totalServiceExperts', 'verifiedExperts',
            'totalNews', 'publishedNews', 'pendingNews',
            'totalPlaces', 'activePlaces',
            'totalRevenue', 'monthlyRevenue', 'todayRevenue',
            'totalEnquiries', 'newEnquiries', 'todayEnquiries',
            'recentUsers', 'recentListings', 'recentPayments', 'recentEnquiries',
            'userRegistrations', 'revenueData'
        ));
    }

    public function analytics()
    {
        // Top performing content
        $topListings = Listing::where('status', 'active')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();

        $topJobs = Job::where('status', 'active')
            ->orderBy('applications_count', 'desc')
            ->limit(10)
            ->get();

        $topProducts = Product::where('status', 'active')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();

        $topServiceExperts = ServiceExpert::where('is_active', true)
            ->orderBy('total_bookings', 'desc')
            ->limit(10)
            ->get();

        $topPlaces = Place::where('status', 'active')
            ->orderBy('view_count', 'desc')
            ->limit(10)
            ->get();

        // Category performance
        $listingsByCategory = Listing::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('count', 'desc')
            ->get();

        $jobsByCategory = Job::selectRaw('category_id, COUNT(*) as count')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('count', 'desc')
            ->get();

        // Location performance
        $listingsByLocation = Listing::selectRaw('location_id, COUNT(*) as count')
            ->groupBy('location_id')
            ->with('location')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'topListings', 'topJobs', 'topProducts', 'topServiceExperts', 'topPlaces',
            'listingsByCategory', 'jobsByCategory', 'listingsByLocation'
        ));
    }
}
