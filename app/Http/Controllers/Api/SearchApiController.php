<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessListing;
use App\Models\Event;
use App\Models\JobListing;
use App\Models\Product;
use App\Models\Classified;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchApiController extends Controller
{
    /**
     * Global search across all content types
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type', 'all'); // all, listings, events, jobs, products, classifieds
        $category = $request->get('category');
        $location = $request->get('location');
        $limit = $request->get('limit', 20);

        $results = [];

        if ($type === 'all' || $type === 'listings') {
            $listingsQuery = BusinessListing::where('status', 'approved')
                ->where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('tags', 'like', "%{$query}%");
                });

            if ($category) {
                $listingsQuery->where('category_id', $category);
            }
            if ($location) {
                $listingsQuery->where('location_id', $location);
            }

            $results['listings'] = $listingsQuery->with(['category', 'location', 'user'])
                ->take($limit)
                ->get()
                ->map(function ($listing) {
                    return [
                        'id' => $listing->id,
                        'type' => 'listing',
                        'title' => $listing->title,
                        'description' => substr($listing->description, 0, 150),
                        'image' => $listing->featured_image ? asset('storage/' . $listing->featured_image) : null,
                        'category' => $listing->category->name ?? null,
                        'location' => $listing->location->name ?? null,
                        'rating' => $listing->average_rating,
                        'reviews_count' => $listing->total_reviews,
                        'slug' => $listing->slug,
                        'url' => route('listings.show', $listing->slug),
                    ];
                });
        }

        if ($type === 'all' || $type === 'events') {
            $eventsQuery = Event::where('status', 'approved')
                ->where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });

            if ($location) {
                $eventsQuery->where('location_id', $location);
            }

            $results['events'] = $eventsQuery->with(['location', 'user'])
                ->take($limit)
                ->get()
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'type' => 'event',
                        'title' => $event->title,
                        'description' => substr($event->description, 0, 150),
                        'image' => $event->featured_image ? asset('storage/' . $event->featured_image) : null,
                        'start_date' => $event->start_date->format('Y-m-d H:i:s'),
                        'location' => $event->location->name ?? $event->venue,
                        'is_free' => $event->is_free,
                        'entry_fee' => $event->entry_fee,
                        'slug' => $event->slug,
                        'url' => route('events.show', $event->slug),
                    ];
                });
        }

        if ($type === 'all' || $type === 'jobs') {
            $jobsQuery = JobListing::where('status', 'approved')
                ->where('is_active', true)
                ->where('application_deadline', '>=', now())
                ->where(function($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });

            if ($category) {
                $jobsQuery->where('category_id', $category);
            }
            if ($location) {
                $jobsQuery->where('location_id', $location);
            }

            $results['jobs'] = $jobsQuery->with(['category', 'location', 'user'])
                ->take($limit)
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'type' => 'job',
                        'title' => $job->title,
                        'description' => substr($job->description, 0, 150),
                        'company' => $job->user->name,
                        'category' => $job->category->name ?? null,
                        'location' => $job->location->name ?? null,
                        'job_type' => $job->job_type,
                        'salary_min' => $job->salary_min,
                        'salary_max' => $job->salary_max,
                        'applications_count' => $job->applications_count,
                        'slug' => $job->slug,
                        'url' => route('jobs.show', $job->slug),
                    ];
                });
        }

        if ($type === 'all' || $type === 'products') {
            $productsQuery = Product::where('status', 'approved')
                ->where('is_active', true)
                ->where(function($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%");
                });

            if ($category) {
                $productsQuery->where('category_id', $category);
            }

            $results['products'] = $productsQuery->with(['category', 'user'])
                ->take($limit)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'type' => 'product',
                        'name' => $product->name,
                        'description' => substr($product->description, 0, 150),
                        'image' => $product->featured_image ? asset('storage/' . $product->featured_image) : null,
                        'category' => $product->category->name ?? null,
                        'regular_price' => $product->regular_price,
                        'sale_price' => $product->sale_price,
                        'rating' => $product->average_rating,
                        'reviews_count' => $product->total_reviews,
                        'slug' => $product->slug,
                        'url' => route('products.show', $product->slug),
                    ];
                });
        }

        // Log search
        DB::table('searches')->insert([
            'user_id' => auth()->id(),
            'query' => $query,
            'type' => $type,
            'category_id' => $category,
            'location_id' => $location,
            'results_count' => collect($results)->flatten(1)->count(),
            'created_at' => now(),
        ]);

        return response()->json([
            'query' => $query,
            'type' => $type,
            'total_results' => collect($results)->flatten(1)->count(),
            'results' => $results,
        ]);
    }

    /**
     * Autocomplete suggestions
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        $limit = $request->get('limit', 10);

        $suggestions = [];

        // Listings
        $listings = BusinessListing::where('status', 'approved')
            ->where('title', 'like', "{$query}%")
            ->take($limit)
            ->pluck('title');

        $suggestions = array_merge($suggestions, $listings->toArray());

        // Events
        $events = Event::where('status', 'approved')
            ->where('title', 'like', "{$query}%")
            ->take($limit)
            ->pluck('title');

        $suggestions = array_merge($suggestions, $events->toArray());

        // Jobs
        $jobs = JobListing::where('status', 'approved')
            ->where('title', 'like', "{$query}%")
            ->take($limit)
            ->pluck('title');

        $suggestions = array_merge($suggestions, $jobs->toArray());

        // Remove duplicates and limit
        $suggestions = array_unique($suggestions);
        $suggestions = array_slice($suggestions, 0, $limit);

        return response()->json([
            'suggestions' => array_values($suggestions),
        ]);
    }

    /**
     * Popular search suggestions
     */
    public function suggestions()
    {
        $popularSearches = DB::table('searches')
            ->select('query', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->take(10)
            ->pluck('query');

        return response()->json([
            'popular_searches' => $popularSearches,
        ]);
    }
}
