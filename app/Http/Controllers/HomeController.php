<?php

namespace App\Http\Controllers;

use App\Models\BusinessListing;
use App\Models\Category;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Location;
use App\Models\Product;
use App\Models\Classified;
use App\Models\ServiceExpert;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Featured categories
        $categories = Category::where('type', 'business')
            ->where('is_featured', true)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->take(12)
            ->get();

        // Featured listings
        $featuredListings = BusinessListing::where('is_featured', true)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->with(['user', 'category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Latest listings
        $latestListings = BusinessListing::where('status', 'approved')
            ->where('is_active', true)
            ->with(['user', 'category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Upcoming events
        $upcomingEvents = Event::where('status', 'approved')
            ->where('is_active', true)
            ->where('start_date', '>=', now())
            ->with(['user', 'location'])
            ->orderBy('start_date')
            ->take(6)
            ->get();

        // Latest jobs
        $latestJobs = JobListing::where('status', 'approved')
            ->where('is_active', true)
            ->where('application_deadline', '>=', now())
            ->with(['user', 'category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Featured products
        $featuredProducts = Product::where('is_featured', true)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Latest classifieds
        $latestClassifieds = Classified::where('status', 'active')
            ->where('expires_at', '>=', now())
            ->with(['user', 'category', 'location'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Service experts
        $serviceExperts = ServiceExpert::where('status', 'approved')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Popular locations
        $locations = Location::where('type', 'city')
            ->where('is_popular', true)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->take(12)
            ->get();

        // Statistics
        $stats = [
            'total_listings' => BusinessListing::where('status', 'approved')->count(),
            'total_users' => \App\Models\User::where('is_active', true)->count(),
            'total_events' => Event::where('status', 'approved')->count(),
            'total_jobs' => JobListing::where('status', 'approved')->count(),
        ];

        return view('home.index', compact(
            'categories',
            'featuredListings',
            'latestListings',
            'upcomingEvents',
            'latestJobs',
            'featuredProducts',
            'latestClassifieds',
            'serviceExperts',
            'locations',
            'stats'
        ));
    }
}
