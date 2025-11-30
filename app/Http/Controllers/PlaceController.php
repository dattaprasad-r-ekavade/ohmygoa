<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Location;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of places.
     */
    public function index(Request $request)
    {
        $query = Place::active()
            ->with('location')
            ->orderBy('is_featured', 'desc')
            ->orderBy('is_popular', 'desc')
            ->orderBy('average_rating', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $places = $query->paginate(12);
        
        $featuredPlaces = Place::active()->featured()->take(6)->get();
        $popularPlaces = Place::active()->popular()->take(6)->get();
        $categories = Place::getCategories();
        $locations = Location::where('type', 'city')->orderBy('name')->get();

        return view('places.index', compact('places', 'featuredPlaces', 'popularPlaces', 'categories', 'locations'));
    }

    /**
     * Display the specified place.
     */
    public function show(string $slug)
    {
        $place = Place::where('slug', $slug)
            ->active()
            ->with('location')
            ->firstOrFail();

        // Increment view count
        $place->incrementViewCount();

        // Get nearby places
        $nearbyPlaces = Place::active()
            ->where('id', '!=', $place->id)
            ->where('location_id', $place->location_id)
            ->orderBy('average_rating', 'desc')
            ->take(4)
            ->get();

        // Get similar places by category
        $similarPlaces = Place::active()
            ->where('id', '!=', $place->id)
            ->where('category', $place->category)
            ->orderBy('average_rating', 'desc')
            ->take(4)
            ->get();

        return view('places.show', compact('place', 'nearbyPlaces', 'similarPlaces'));
    }

    /**
     * Display places by category.
     */
    public function category(string $category)
    {
        if (!array_key_exists($category, Place::getCategories())) {
            abort(404);
        }

        $places = Place::active()
            ->byCategory($category)
            ->with('location')
            ->orderBy('is_featured', 'desc')
            ->orderBy('average_rating', 'desc')
            ->paginate(12);

        $categoryName = Place::getCategories()[$category];
        $locations = Location::where('type', 'city')->orderBy('name')->get();

        return view('places.category', compact('places', 'category', 'categoryName', 'locations'));
    }

    /**
     * Display places by location.
     */
    public function location(int $locationId)
    {
        $location = Location::findOrFail($locationId);

        $places = Place::active()
            ->byLocation($locationId)
            ->orderBy('is_featured', 'desc')
            ->orderBy('average_rating', 'desc')
            ->paginate(12);

        $categories = Place::getCategories();

        return view('places.location', compact('places', 'location', 'categories'));
    }
}
