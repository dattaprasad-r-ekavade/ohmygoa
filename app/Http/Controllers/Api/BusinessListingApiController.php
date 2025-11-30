<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessListingResource;
use App\Models\BusinessListing;
use Illuminate\Http\Request;

class BusinessListingApiController extends Controller
{
    /**
     * Get all business listings.
     */
    public function index(Request $request)
    {
        $query = BusinessListing::where('status', 'approved')
            ->with(['user', 'category']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location_id')) {
            $query->where('city', $request->location_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $listings = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return BusinessListingResource::collection($listings);
    }

    /**
     * Get single business listing.
     */
    public function show($id)
    {
        $listing = BusinessListing::where('status', 'approved')
            ->with(['user', 'category'])
            ->findOrFail($id);

        // Increment view count
        $listing->increment('view_count');

        return new BusinessListingResource($listing);
    }

    /**
     * Get user's business listings.
     */
    public function myListings(Request $request)
    {
        $listings = BusinessListing::where('user_id', $request->user()->id)
            ->with(['category'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return BusinessListingResource::collection($listings);
    }

    /**
     * Create business listing.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string',
            'city' => 'required|exists:locations,id',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $listing = new BusinessListing($request->all());
        $listing->user_id = $request->user()->id;
        $listing->status = 'pending';
        $listing->slug = \App\Helpers\SlugHelper::generate($request->title, BusinessListing::class);
        
        // Handle images upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('business-listings', 'public');
                $images[] = $path;
            }
            $listing->images = json_encode($images);
        }

        $listing->save();

        return response()->json([
            'message' => 'Business listing created successfully. Pending admin approval.',
            'listing' => new BusinessListingResource($listing),
        ], 201);
    }

    /**
     * Update business listing.
     */
    public function update(Request $request, $id)
    {
        $listing = BusinessListing::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'address' => 'sometimes|string',
            'city' => 'sometimes|exists:locations,id',
            'phone' => 'sometimes|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ]);

        $listing->fill($request->all());
        
        if ($request->has('title')) {
            $listing->slug = \App\Helpers\SlugHelper::generate($request->title, BusinessListing::class, $listing->id);
        }

        $listing->save();

        return response()->json([
            'message' => 'Business listing updated successfully',
            'listing' => new BusinessListingResource($listing),
        ]);
    }

    /**
     * Delete business listing.
     */
    public function destroy(Request $request, $id)
    {
        $listing = BusinessListing::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $listing->delete();

        return response()->json([
            'message' => 'Business listing deleted successfully',
        ]);
    }
}
