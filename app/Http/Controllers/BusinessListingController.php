<?php

namespace App\Http\Controllers;

use App\Models\BusinessListing;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BusinessListingController extends Controller
{
    /**
     * Display a listing of business listings.
     */
    public function index(Request $request): View
    {
        $query = BusinessListing::with(['category', 'location', 'user'])
            ->active()
            ->approved();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Verified filter
        if ($request->filled('verified')) {
            $query->verified();
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if ($sortBy === 'rating') {
            $query->orderBy('average_rating', $sortOrder);
        } elseif ($sortBy === 'popular') {
            $query->orderBy('views_count', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $listings = $query->paginate(12);

        return view('listings.index', [
            'listings' => $listings,
            'categories' => Category::active()->ofType('listing')->get(),
            'locations' => Location::active()->popular()->get(),
        ]);
    }

    /**
     * Display the specified business listing.
     */
    public function show(string $slug): View
    {
        $listing = BusinessListing::with(['category', 'location', 'user', 'images'])
            ->where('slug', $slug)
            ->active()
            ->approved()
            ->firstOrFail();

        // Increment view count
        $listing->incrementViewCount();

        // Load reviews with pagination
        $reviews = $listing->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        // Similar listings
        $similarListings = BusinessListing::where('category_id', $listing->category_id)
            ->where('id', '!=', $listing->id)
            ->active()
            ->approved()
            ->limit(4)
            ->get();

        return view('listings.show', [
            'listing' => $listing,
            'reviews' => $reviews,
            'similarListings' => $similarListings,
            'isBookmarked' => auth()->check() ? $listing->isBookmarkedBy(auth()->id()) : false,
            'isFollowing' => auth()->check() ? $listing->isFollowedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new business listing.
     */
    public function create(): View
    {
        Gate::authorize('create-listing');

        return view('listings.create', [
            'categories' => Category::active()->ofType('listing')->root()->get(),
            'locations' => Location::active()->states()->get(),
        ]);
    }

    /**
     * Store a newly created business listing.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-listing');

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'business_hours' => 'nullable|array',
            'social_media' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:4096',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending'; // Requires admin approval

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('listings/logos', 'public');
        }

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('listings/banners', 'public');
        }

        $listing = BusinessListing::create($validated);

        // Handle multiple images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('listings/images', 'public');
                $listing->images()->create([
                    'image_path' => $path,
                    'position' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('business.listings.index')
            ->with('success', 'Business listing created successfully and is pending approval.');
    }

    /**
     * Show the form for editing the business listing.
     */
    public function edit(BusinessListing $listing): View
    {
        Gate::authorize('update', $listing);

        return view('listings.edit', [
            'listing' => $listing,
            'categories' => Category::active()->ofType('listing')->root()->get(),
            'locations' => Location::active()->states()->get(),
        ]);
    }

    /**
     * Update the specified business listing.
     */
    public function update(Request $request, BusinessListing $listing)
    {
        Gate::authorize('update', $listing);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'business_hours' => 'nullable|array',
            'social_media' => 'nullable|array',
            'logo' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:4096',
        ]);

        // Handle file uploads
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('listings/logos', 'public');
        }

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = $request->file('banner_image')->store('listings/banners', 'public');
        }

        $listing->update($validated);

        return redirect()->route('business.listings.show', $listing)
            ->with('success', 'Business listing updated successfully.');
    }

    /**
     * Remove the specified business listing.
     */
    public function destroy(BusinessListing $listing)
    {
        Gate::authorize('delete', $listing);

        $listing->delete();

        return redirect()->route('business.listings.index')
            ->with('success', 'Business listing deleted successfully.');
    }

    /**
     * Toggle bookmark for listing.
     */
    public function toggleBookmark(BusinessListing $listing)
    {
        $isBookmarked = $listing->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }

    /**
     * Toggle follow for listing.
     */
    public function toggleFollow(BusinessListing $listing)
    {
        $follow = $listing->followers()->where('user_id', auth()->id())->first();

        if ($follow) {
            $follow->delete();
            $isFollowing = false;
        } else {
            $listing->followers()->create(['user_id' => auth()->id()]);
            $isFollowing = true;
        }

        return response()->json([
            'following' => $isFollowing,
            'message' => $isFollowing ? 'Following business' : 'Unfollowed business',
        ]);
    }
}
