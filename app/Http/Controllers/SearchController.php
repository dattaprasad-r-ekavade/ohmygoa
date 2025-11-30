<?php

namespace App\Http\Controllers;

use App\Models\BusinessListing;
use App\Models\Classified;
use App\Models\Coupon;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Product;
use App\Models\ServiceExpert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Show search page.
     */
    public function index(Request $request)
    {
        return view('search.index', [
            'query' => $request->input('q'),
            'type' => $request->input('type', 'all'),
        ]);
    }

    /**
     * Perform search and return results.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'all');
        $categoryId = $request->input('category_id');
        $locationId = $request->input('location_id');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = $request->input('per_page', 20);

        $results = [];

        // Search based on type
        if ($type === 'all' || $type === 'listings') {
            $results['listings'] = $this->searchBusinessListings($query, $categoryId, $locationId, $perPage);
        }

        if ($type === 'all' || $type === 'events') {
            $results['events'] = $this->searchEvents($query, $categoryId, $locationId, $startDate, $endDate, $perPage);
        }

        if ($type === 'all' || $type === 'jobs') {
            $results['jobs'] = $this->searchJobs($query, $categoryId, $locationId, $perPage);
        }

        if ($type === 'all' || $type === 'products') {
            $results['products'] = $this->searchProducts($query, $categoryId, $minPrice, $maxPrice, $perPage);
        }

        if ($type === 'all' || $type === 'coupons') {
            $results['coupons'] = $this->searchCoupons($query, $categoryId, $minPrice, $maxPrice, $perPage);
        }

        if ($type === 'all' || $type === 'classifieds') {
            $results['classifieds'] = $this->searchClassifieds($query, $categoryId, $locationId, $perPage);
        }

        if ($type === 'all' || $type === 'service-experts') {
            $results['service_experts'] = $this->searchServiceExperts($query, $categoryId, $locationId, $perPage);
        }

        return view('search.results', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'filters' => $request->only(['category_id', 'location_id', 'min_price', 'max_price', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Search business listings.
     */
    protected function searchBusinessListings($query, $categoryId, $locationId, $perPage)
    {
        $search = BusinessListing::where('status', 'approved')
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
            $search->where('city', $locationId);
        }

        return $search->with(['user', 'category'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Search events.
     */
    protected function searchEvents($query, $categoryId, $locationId, $startDate, $endDate, $perPage)
    {
        $search = Event::where('status', 'approved')
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('city', $locationId);
        }

        if ($startDate) {
            $search->where('start_date', '>=', $startDate);
        }

        if ($endDate) {
            $search->where('end_date', '<=', $endDate);
        }

        return $search->with(['user', 'category'])
            ->orderBy('start_date')
            ->paginate($perPage);
    }

    /**
     * Search job listings.
     */
    protected function searchJobs($query, $categoryId, $locationId, $perPage)
    {
        $search = JobListing::where('status', 'active')
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('job_title', 'like', "%{$query}%")
                      ->orWhere('job_description', 'like', "%{$query}%")
                      ->orWhere('required_skills', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('job_category', $categoryId);
        }

        if ($locationId) {
            $search->where('job_city', $locationId);
        }

        return $search->with(['user'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Search products.
     */
    protected function searchProducts($query, $categoryId, $minPrice, $maxPrice, $perPage)
    {
        $search = Product::where('status', 'approved')
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
     * Search classifieds.
     */
    protected function searchClassifieds($query, $categoryId, $locationId, $perPage)
    {
        $search = Classified::where('status', 'approved')
            ->where('expires_at', '>', now())
            ->where(function ($q) use ($query) {
                if ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                }
            });

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($locationId) {
            $search->where('city', $locationId);
        }

        return $search->with(['user', 'category'])
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
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
}
