<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlaceController extends Controller
{
    /**
     * Display a listing of places.
     */
    public function index(Request $request)
    {
        $query = Place::with('location')
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $places = $query->paginate(20);
        $categories = Place::getCategories();

        return view('admin.places.index', compact('places', 'categories'));
    }

    /**
     * Show the form for creating a new place.
     */
    public function create()
    {
        $categories = Place::getCategories();
        $facilities = Place::getFacilities();
        $locations = Location::where('type', 'city')->orderBy('name')->get();

        return view('admin.places.create', compact('categories', 'facilities', 'locations'));
    }

    /**
     * Store a newly created place in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'highlights' => 'nullable|string',
            'category' => 'required|in:beach,church,temple,fort,museum,waterfall,viewpoint,market,wildlife,other',
            'location_id' => 'required|exists:locations,id',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'timings' => 'nullable|array',
            'entry_fee' => 'nullable|string|max:100',
            'best_time_to_visit' => 'nullable|string',
            'how_to_reach' => 'nullable|string',
            'facilities' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:pending,approved,rejected',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('places', 'public');
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('places', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active'] = $request->boolean('is_active');

        Place::create($validated);

        return redirect()->route('admin.places.index')
            ->with('success', 'Place created successfully.');
    }

    /**
     * Display the specified place.
     */
    public function show(Place $place)
    {
        $place->load('location');
        return view('admin.places.show', compact('place'));
    }

    /**
     * Show the form for editing the specified place.
     */
    public function edit(Place $place)
    {
        $categories = Place::getCategories();
        $facilities = Place::getFacilities();
        $locations = Location::where('type', 'city')->orderBy('name')->get();

        return view('admin.places.edit', compact('place', 'categories', 'facilities', 'locations'));
    }

    /**
     * Update the specified place in storage.
     */
    public function update(Request $request, Place $place)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'highlights' => 'nullable|string',
            'category' => 'required|in:beach,church,temple,fort,museum,waterfall,viewpoint,market,wildlife,other',
            'location_id' => 'required|exists:locations,id',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'timings' => 'nullable|array',
            'entry_fee' => 'nullable|string|max:100',
            'best_time_to_visit' => 'nullable|string',
            'how_to_reach' => 'nullable|string',
            'facilities' => 'nullable|array',
            'contact_info' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'is_active' => 'boolean',
            'status' => 'required|in:pending,approved,rejected',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($place->featured_image) {
                Storage::disk('public')->delete($place->featured_image);
            }

            $validated['featured_image'] = $request->file('featured_image')
                ->store('places', 'public');
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            $imagePaths = $place->images ?? [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('places', 'public');
            }
            $validated['images'] = $imagePaths;
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_active'] = $request->boolean('is_active');

        $place->update($validated);

        return redirect()->route('admin.places.index')
            ->with('success', 'Place updated successfully.');
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Place $place)
    {
        $place->update(['is_featured' => !$place->is_featured]);

        $status = $place->is_featured ? 'featured' : 'unfeatured';

        return redirect()->back()
            ->with('success', "Place marked as {$status}.");
    }

    /**
     * Toggle popular status.
     */
    public function togglePopular(Place $place)
    {
        $place->update(['is_popular' => !$place->is_popular]);

        $status = $place->is_popular ? 'popular' : 'unpopular';

        return redirect()->back()
            ->with('success', "Place marked as {$status}.");
    }

    /**
     * Remove the specified place from storage.
     */
    public function destroy(Place $place)
    {
        // Delete featured image
        if ($place->featured_image) {
            Storage::disk('public')->delete($place->featured_image);
        }

        // Delete all images
        if ($place->images) {
            foreach ($place->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $place->delete();

        return redirect()->route('admin.places.index')
            ->with('success', 'Place deleted successfully.');
    }

    /**
     * Remove an image from the place.
     */
    public function removeImage(Request $request, Place $place)
    {
        $imageIndex = $request->input('image_index');
        
        if (isset($place->images[$imageIndex])) {
            $imagePath = $place->images[$imageIndex];
            Storage::disk('public')->delete($imagePath);
            
            $images = $place->images;
            unset($images[$imageIndex]);
            $place->update(['images' => array_values($images)]);
            
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
