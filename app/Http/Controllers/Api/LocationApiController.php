<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
    /**
     * Get all locations
     */
    public function index(Request $request)
    {
        $type = $request->get('type'); // country, state, city, area
        $parentId = $request->get('parent_id');
        $popularOnly = $request->get('popular_only', false);

        $query = Location::where('is_active', true)->orderBy('display_order');

        if ($type) {
            $query->where('type', $type);
        }

        if ($parentId) {
            $query->where('parent_id', $parentId);
        }

        if ($popularOnly) {
            $query->where('is_popular', true);
        }

        $locations = $query->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'slug' => $location->slug,
                'type' => $location->type,
                'parent_id' => $location->parent_id,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'is_popular' => $location->is_popular,
                'listings_count' => $location->listings()->count(),
                'children_count' => $location->children()->count(),
            ];
        });

        return response()->json([
            'locations' => $locations,
            'total' => $locations->count(),
        ]);
    }

    /**
     * Get single location
     */
    public function show($id)
    {
        $location = Location::where('is_active', true)->findOrFail($id);

        return response()->json([
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'slug' => $location->slug,
                'type' => $location->type,
                'parent_id' => $location->parent_id,
                'parent' => $location->parent ? [
                    'id' => $location->parent->id,
                    'name' => $location->parent->name,
                    'slug' => $location->parent->slug,
                ] : null,
                'children' => $location->children()->where('is_active', true)->get()->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                        'type' => $child->type,
                    ];
                }),
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'is_popular' => $location->is_popular,
                'listings_count' => $location->listings()->where('status', 'approved')->count(),
                'events_count' => $location->events()->where('status', 'approved')->count(),
                'jobs_count' => $location->jobListings()->where('status', 'approved')->count(),
            ],
        ]);
    }

    /**
     * Get listings in location
     */
    public function listings($id, Request $request)
    {
        $location = Location::where('is_active', true)->findOrFail($id);
        $limit = $request->get('limit', 20);

        $listings = $location->listings()
            ->where('status', 'approved')
            ->where('is_active', true)
            ->with(['category', 'user'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'slug' => $location->slug,
            ],
            'listings' => $listings->items(),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
        ]);
    }
}
