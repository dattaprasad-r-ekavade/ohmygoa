<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\Job;
use App\Models\Product;
use App\Models\Event;
use App\Models\Classified;
use App\Models\Payment;
use App\Models\Search;
use App\Models\Enquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    // User Activity Report
    public function userActivity(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        $newUsers = User::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $activeUsers = User::whereHas('listings', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->orWhereHas('jobs', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->count();

        // Daily user registrations
        $dailyRegistrations = User::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Users by role
        $usersByRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->get();

        return view('admin.reports.user-activity', compact(
            'newUsers', 'activeUsers', 'dailyRegistrations', 'usersByRole', 'dateFrom', 'dateTo'
        ));
    }

    // Content Performance Report
    public function contentPerformance(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        // Top listings by views
        $topListings = Listing::whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('view_count', 'desc')
            ->limit(20)
            ->with('user')
            ->get();

        // Top jobs by applications
        $topJobs = Job::whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('applications_count', 'desc')
            ->limit(20)
            ->with('user')
            ->get();

        // Content creation trends
        $contentTrends = [
            'listings' => Listing::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'jobs' => Job::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'products' => Product::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        // Content by status
        $contentByStatus = [
            'listings' => Listing::selectRaw('status, COUNT(*) as count')->groupBy('status')->get(),
            'jobs' => Job::selectRaw('status, COUNT(*) as count')->groupBy('status')->get(),
            'products' => Product::selectRaw('status, COUNT(*) as count')->groupBy('status')->get(),
        ];

        return view('admin.reports.content-performance', compact(
            'topListings', 'topJobs', 'contentTrends', 'contentByStatus', 'dateFrom', 'dateTo'
        ));
    }

    // Financial Report
    public function financial(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        $totalRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('amount');

        $totalCommission = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('commission_amount');

        $totalTransactions = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Daily revenue
        $dailyRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by payment type
        $revenueByType = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('payment_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_type')
            ->get();

        return view('admin.reports.financial', compact(
            'totalRevenue', 'totalCommission', 'totalTransactions', 'averageTransaction',
            'dailyRevenue', 'revenueByType', 'dateFrom', 'dateTo'
        ));
    }

    // Search Analytics Report
    public function searchAnalytics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        // Popular searches
        $popularSearches = Search::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('query, COUNT(*) as count, AVG(results_count) as avg_results')
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit(50)
            ->get();

        // Zero result searches
        $zeroResultSearches = Search::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('results_count', 0)
            ->selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        // Daily search trends
        $dailySearches = Search::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Searches by type
        $searchesByType = Search::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('type')
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.reports.search-analytics', compact(
            'popularSearches', 'zeroResultSearches', 'dailySearches', 'searchesByType', 'dateFrom', 'dateTo'
        ));
    }

    // Location Performance Report
    public function locationPerformance(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        // Listings by location
        $listingsByLocation = Listing::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('location_id, COUNT(*) as count')
            ->groupBy('location_id')
            ->with('location')
            ->orderBy('count', 'desc')
            ->get();

        // Jobs by location
        $jobsByLocation = Job::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('location_id, COUNT(*) as count')
            ->groupBy('location_id')
            ->with('location')
            ->orderBy('count', 'desc')
            ->get();

        // Events by location
        $eventsByLocation = Event::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('location_id, COUNT(*) as count')
            ->groupBy('location_id')
            ->with('location')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.reports.location-performance', compact(
            'listingsByLocation', 'jobsByLocation', 'eventsByLocation', 'dateFrom', 'dateTo'
        ));
    }

    // Category Performance Report
    public function categoryPerformance(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        // Listings by category
        $listingsByCategory = Listing::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('category_id, COUNT(*) as count, AVG(view_count) as avg_views')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('count', 'desc')
            ->get();

        // Jobs by category
        $jobsByCategory = Job::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('category_id, COUNT(*) as count, AVG(applications_count) as avg_applications')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('count', 'desc')
            ->get();

        // Products by category
        $productsByCategory = Product::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('category_id, COUNT(*) as count, SUM(total_sales) as total_sales')
            ->groupBy('category_id')
            ->with('category')
            ->orderBy('count', 'desc')
            ->get();

        return view('admin.reports.category-performance', compact(
            'listingsByCategory', 'jobsByCategory', 'productsByCategory', 'dateFrom', 'dateTo'
        ));
    }

    // Enquiry Report
    public function enquiries(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonth();
        $dateTo = $request->date_to ?? now();

        $totalEnquiries = Enquiry::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $respondedEnquiries = Enquiry::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['replied', 'closed'])
            ->count();
        $responseRate = $totalEnquiries > 0 ? ($respondedEnquiries / $totalEnquiries) * 100 : 0;

        // Enquiries by status
        $enquiriesByStatus = Enquiry::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Enquiries by type
        $enquiriesByType = Enquiry::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('enquirable_type, COUNT(*) as count')
            ->groupBy('enquirable_type')
            ->get();

        // Daily enquiries
        $dailyEnquiries = Enquiry::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.enquiries', compact(
            'totalEnquiries', 'respondedEnquiries', 'responseRate',
            'enquiriesByStatus', 'enquiriesByType', 'dailyEnquiries', 'dateFrom', 'dateTo'
        ));
    }

    // Export functionality
    public function export(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:users,content,financial,search,location,category,enquiries',
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'required|date',
            'date_to' => 'required|date'
        ]);

        // This would integrate with Laravel Excel or similar package
        // For now, return a placeholder response
        
        return back()->with('success', 'Report export initiated! You will receive an email when ready.');
    }
}
