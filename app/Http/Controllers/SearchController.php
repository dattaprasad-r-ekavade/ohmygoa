<?php

namespace App\Http\Controllers;

use App\Models\BusinessListing;
use App\Models\Category;
use App\Models\Classified;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Location;
use App\Models\Product;
use App\Models\SavedSearch;
use App\Models\ServiceExpert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Show search page with results.
     */
    public function index(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $categoryId = $request->input('category_id');
        $locationId = $request->input('location_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $minRating = $request->input('min_rating');
        $sortBy = $request->input('sort_by', 'relevance');
        $perPage = $request->input('per_page', 20);

        // Get filters data
        $categories = Category::where('is_active', true)
            ->where('type', $type !== 'all' ? $type : 'business')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $locations = Location::where('is_active', true)
            ->where('type', 'city')
            ->orderBy('name')
            ->get();

        // Perform search if query exists
        $results = [];
        $totalResults = 0;

        if ($query) {
            $results = $this->performSearch($request);
            $totalResults = collect($results)->flatten(1)->count();

            // Log search
            $this->logSearch($query, $type, $categoryId, $locationId, $totalResults);
        }

        // Get user's saved searches
        $savedSearches = auth()->check() 
            ? auth()->user()->savedSearches()->latest()->take(5)->get()
            : collect();

        return view('search.index', compact(
            'query',
            'type',
            'results',
            'totalResults',
            'categories',
            'locations',
            'savedSearches',
            'categoryId',
            'locationId',
            'minPrice',
            'maxPrice',
            'minRating',
            'sortBy'
        ));
    }

    /**
     * Perform comprehensive search.
     */
    protected function performSearch(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $categoryId = $request->input('category_id');
        $locationId = $request->input('location_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $minRating = $request->input('min_rating');
        $sortBy = $request->input('sort_by', 'relevance');
        $perPage = $request->input('per_page', 20);

        $results = [];

        // Search based on type
        if ($type === 'all' || $type === 'listings') {
            $results['listings'] = $this->searchBusinessListings(
                $query, $categoryId, $locationId, $minRating, $sortBy, $perPage
            );
        }

        if ($type === 'all' || $type === 'events') {
            $results['events'] = $this->searchEvents(
                $query, $categoryId, $locationId, $sortBy, $perPage
            );
        }

        if ($type === 'all' || $type === 'jobs') {
            $results['jobs'] = $this->searchJobs(
                $query, $categoryId, $locationId, $sortBy, $perPage
            );
        }

        if ($type === 'all' || $type === 'products') {
            $results['products'] = $this->searchProducts(
                $query, $categoryId, $minPrice, $maxPrice, $minRating, $sortBy, $perPage
            );
        }

        if ($type === 'all' || $type === 'classifieds') {
            $results['classifieds'] = $this->searchClassifieds(
                $query, $categoryId, $locationId, $minPrice, $maxPrice, $sortBy, $perPage
            );
        }

        return $results;
    }

    /**
     * Log search query.
     */
    protected function logSearch($query, $type, $categoryId, $locationId, $resultsCount)
    {
        DB::table('searches')->insert([
            'user_id' => auth()->id(),
            'query' => $query,
            'type' => $type,
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'results_count' => $resultsCount,
            'created_at' => now(),
        ]);
    }

    /**
     * Search business listings with advanced filters.
     */
    protected function searchBusinessListings($query, $categoryId, $locationId, $minRating, $sortBy, $perPage)
    {
        $search = BusinessListing::where('status', 'approved')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('tags', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('location_id', $locationId);
        }

        if ($minRating) {
            $search->where('average_rating', '>=', $minRating);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'rating':
                $search->orderByDesc('average_rating');
                break;
            case 'reviews':
                $search->orderByDesc('total_reviews');
                break;
            case 'newest':
                $search->orderByDesc('created_at');
                break;
            case 'oldest':
                $search->orderBy('created_at');
                break;
            default: // relevance
                $search->orderByDesc('is_featured')
                       ->orderByDesc('average_rating')
                       ->orderByDesc('total_reviews');
        }

        return $search->with(['category', 'location', 'user'])
            ->paginate($perPage);
    }

    /**
     * Search events with advanced filters.
     */
    protected function searchEvents($query, $categoryId, $locationId, $sortBy, $perPage)
    {
        $search = Event::where('status', 'approved')
            ->where('is_active', true)
            ->where('start_date', '>=', now())
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('venue', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('location_id', $locationId);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'date':
                $search->orderBy('start_date');
                break;
            case 'popular':
                $search->orderByDesc('attendees_count');
                break;
            case 'newest':
                $search->orderByDesc('created_at');
                break;
            default: // relevance
                $search->orderByDesc('is_featured')
                       ->orderBy('start_date');
        }

        return $search->with(['category', 'location', 'user'])
            ->paginate($perPage);
    }

    /**
     * Search job listings with advanced filters.
     */
    protected function searchJobs($query, $categoryId, $locationId, $sortBy, $perPage)
    {
        $search = JobListing::where('status', 'approved')
            ->where('is_active', true)
            ->where('application_deadline', '>=', now())
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('skills_required', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('location_id', $locationId);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'salary':
                $search->orderByDesc('salary_max');
                break;
            case 'applicants':
                $search->orderBy('applications_count');
                break;
            case 'deadline':
                $search->orderBy('application_deadline');
                break;
            case 'newest':
                $search->orderByDesc('created_at');
                break;
            default: // relevance
                $search->orderByDesc('is_featured')
                       ->orderByDesc('created_at');
        }

        return $search->with(['category', 'location', 'user'])
            ->paginate($perPage);
    }

    /**
     * Search products with advanced filters.
     */
    protected function searchProducts($query, $categoryId, $minPrice, $maxPrice, $minRating, $sortBy, $perPage)
    {
        $search = Product::where('status', 'approved')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('tags', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($minPrice) {
            $search->where(function($q) use ($minPrice) {
                $q->where('sale_price', '>=', $minPrice)
                  ->orWhere(function($q2) use ($minPrice) {
                      $q2->whereNull('sale_price')
                         ->where('regular_price', '>=', $minPrice);
                  });
            });
        }

        if ($maxPrice) {
            $search->where(function($q) use ($maxPrice) {
                $q->where('sale_price', '<=', $maxPrice)
                  ->orWhere(function($q2) use ($maxPrice) {
                      $q2->whereNull('sale_price')
                         ->where('regular_price', '<=', $maxPrice);
                  });
            });
        }

        if ($minRating) {
            $search->where('average_rating', '>=', $minRating);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $search->orderByRaw('COALESCE(sale_price, regular_price) ASC');
                break;
            case 'price_high':
                $search->orderByRaw('COALESCE(sale_price, regular_price) DESC');
                break;
            case 'rating':
                $search->orderByDesc('average_rating');
                break;
            case 'reviews':
                $search->orderByDesc('total_reviews');
                break;
            case 'newest':
                $search->orderByDesc('created_at');
                break;
            default: // relevance
                $search->orderByDesc('is_featured')
                       ->orderByDesc('average_rating')
                       ->orderByRaw('COALESCE(sale_price, regular_price) ASC');
        }

        return $search->with(['category', 'user'])
            ->paginate($perPage);
    }

    /**
     * Search coupons.
     */
    protected function searchCoupons($query, $categoryId, $minPrice, $maxPrice, $perPage)
    {
        $search = Coupon::where('status', 'approved')
            ->where('valid_until', '>', now())
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('coupon_code', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($minPrice) {
            $search->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $search->where('price', '<=', $maxPrice);
        }

        return $search->with(['user', 'category'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Search classifieds with advanced filters.
     */
    protected function searchClassifieds($query, $categoryId, $locationId, $minPrice, $maxPrice, $sortBy, $perPage)
    {
        $search = Classified::where('status', 'approved')
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('tags', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('location_id', $locationId);
        }

        if ($minPrice) {
            $search->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $search->where('price', '<=', $maxPrice);
        }

        // Apply sorting
        switch ($sortBy) {
            case 'price_low':
                $search->orderBy('price');
                break;
            case 'price_high':
                $search->orderByDesc('price');
                break;
            case 'expiry':
                $search->orderBy('expires_at');
                break;
            case 'newest':
                $search->orderByDesc('created_at');
                break;
            default: // relevance
                $search->orderByDesc('is_featured')
                       ->orderByDesc('created_at');
        }

        return $search->with(['category', 'location', 'user'])
            ->paginate($perPage);
    }

    /**
     * Search service experts.
     */
    protected function searchServiceExperts($query, $categoryId, $locationId, $perPage)
    {
        $search = ServiceExpert::where('status', 'approved')
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('expertise', 'like', "%{$query}%")
                      ->orWhere('services_offered', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('city', $locationId);
        }

        return $search->with(['user', 'category'])
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Get autocomplete suggestions.
     */
    public function autocomplete(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $limit = 10;

        $suggestions = [];

        if ($type === 'all' || $type === 'listings') {
            $listings = BusinessListing::where('status', 'approved')
                ->where('title', 'like', "%{$query}%")
                ->select('id', 'title', 'slug')
                ->limit($limit)
                ->get()
                ->map(fn($item) => [
                    'type' => 'listing',
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => route('business-listings.show', $item->slug),
                ]);
            
            $suggestions = array_merge($suggestions, $listings->toArray());
        }

        if ($type === 'all' || $type === 'events') {
            $events = Event::where('status', 'approved')
                ->where('title', 'like', "%{$query}%")
                ->select('id', 'title', 'slug')
                ->limit($limit)
                ->get()
                ->map(fn($item) => [
                    'type' => 'event',
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => route('events.show', $item->slug),
                ]);
            
            $suggestions = array_merge($suggestions, $events->toArray());
        }

        if ($type === 'all' || $type === 'jobs') {
            $jobs = JobListing::where('status', 'active')
                ->where('job_title', 'like', "%{$query}%")
                ->select('id', 'job_title as title', 'slug')
                ->limit($limit)
                ->get()
                ->map(fn($item) => [
                    'type' => 'job',
                    'id' => $item->id,
                    'title' => $item->title,
                    'url' => route('jobs.show', $item->slug),
                ]);
            
            $suggestions = array_merge($suggestions, $jobs->toArray());
        }

        return response()->json([
            'suggestions' => array_slice($suggestions, 0, 15),
        ]);
    }

    /**
     * Save a search for later.
     */
    public function saveSearch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'query' => 'required|string|max:255',
            'filters' => 'nullable|array',
        ]);

        $user = auth()->user();

        $savedSearch = SavedSearch::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'query' => $request->query,
            'filters' => $request->filters ?? [],
        ]);

        return response()->json([
            'message' => 'Search saved successfully',
            'saved_search' => $savedSearch,
        ], 201);
    }

    /**
     * Get user's saved searches.
     */
    public function savedSearches()
    {
        $savedSearches = auth()->user()->savedSearches()
            ->latest()
            ->paginate(20);

        return view('search.saved', compact('savedSearches'));
    }

    /**
     * Delete a saved search.
     */
    public function deleteSavedSearch($id)
    {
        $savedSearch = auth()->user()->savedSearches()->findOrFail($id);
        $savedSearch->delete();

        return response()->json([
            'message' => 'Saved search deleted successfully',
        ]);
    }

    /**
     * Get popular/trending searches.
     */
    public function popularSearches()
    {
        $popularSearches = DB::table('searches')
            ->select('query', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('query')
            ->orderByDesc('count')
            ->take(20)
            ->get();

        return response()->json([
            'popular_searches' => $popularSearches,
        ]);
    }

    /**
     * Get search analytics (admin).
     */
    public function analytics(Request $request)
    {
        $days = $request->input('days', 30);

        $totalSearches = DB::table('searches')
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        $topQueries = DB::table('searches')
            ->select('query', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('query')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        $searchesByType = DB::table('searches')
            ->select('type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('type')
            ->get();

        $avgResultsCount = DB::table('searches')
            ->where('created_at', '>=', now()->subDays($days))
            ->avg('results_count');

        $zeroResultSearches = DB::table('searches')
            ->where('created_at', '>=', now()->subDays($days))
            ->where('results_count', 0)
            ->count();

        return view('admin.search-analytics', compact(
            'totalSearches',
            'topQueries',
            'searchesByType',
            'avgResultsCount',
            'zeroResultSearches',
            'days'
        ));
    }
}
