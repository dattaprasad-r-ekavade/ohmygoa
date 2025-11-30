<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Classified;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClassifiedController extends Controller
{
    /**
     * Display a listing of classified ads.
     */
    public function index(Request $request): View
    {
        $query = Classified::with(['category', 'location', 'user'])
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

        // Condition filter
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Negotiable filter
        if ($request->filled('negotiable')) {
            $query->where('is_negotiable', true);
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');

        if ($sortBy === 'price') {
            $query->orderBy('price', $sortOrder);
        } elseif ($sortBy === 'popular') {
            $query->orderBy('views_count', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $classifieds = $query->paginate(20);

        return view('classifieds.index', [
            'classifieds' => $classifieds,
            'categories' => Category::active()->ofType('classified')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Display the specified classified ad.
     */
    public function show(string $slug): View
    {
        $classified = Classified::with(['category', 'location', 'user'])
            ->where('slug', $slug)
            ->active()
            ->approved()
            ->firstOrFail();

        $classified->incrementViewCount();

        // Similar classifieds
        $similarClassifieds = Classified::where('category_id', $classified->category_id)
            ->where('id', '!=', $classified->id)
            ->active()
            ->approved()
            ->limit(4)
            ->get();

        return view('classifieds.show', [
            'classified' => $classified,
            'similarClassifieds' => $similarClassifieds,
            'isBookmarked' => auth()->check() ? $classified->isBookmarkedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new classified ad.
     */
    public function create(): View
    {
        return view('classifieds.create', [
            'categories' => Category::active()->ofType('classified')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Store a newly created classified ad.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_negotiable' => 'boolean',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string',
            'main_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['expires_at'] = now()->addDays(30); // Default 30 days expiry

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('classifieds', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('classifieds/gallery', 'public');
            }
            $validated['gallery'] = $gallery;
        }

        $classified = Classified::create($validated);

        return redirect()->route('classifieds.index')
            ->with('success', 'Classified ad posted successfully and is pending approval.');
    }

    /**
     * Show the form for editing the classified ad.
     */
    public function edit(Classified $classified): View
    {
        Gate::authorize('update', $classified);

        return view('classifieds.edit', [
            'classified' => $classified,
            'categories' => Category::active()->ofType('classified')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Update the specified classified ad.
     */
    public function update(Request $request, Classified $classified)
    {
        Gate::authorize('update', $classified);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_negotiable' => 'boolean',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string',
            'main_image' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('classifieds', 'public');
        }

        $classified->update($validated);

        return redirect()->route('classifieds.show', $classified->slug)
            ->with('success', 'Classified ad updated successfully.');
    }

    /**
     * Remove the specified classified ad.
     */
    public function destroy(Classified $classified)
    {
        Gate::authorize('delete', $classified);

        $classified->delete();

        return redirect()->route('classifieds.index')
            ->with('success', 'Classified ad deleted successfully.');
    }

    /**
     * Mark classified as sold.
     */
    public function markAsSold(Classified $classified)
    {
        Gate::authorize('update', $classified);

        $classified->update(['status' => 'sold']);

        return redirect()->route('classifieds.show', $classified->slug)
            ->with('success', 'Classified ad marked as sold.');
    }

    /**
     * Renew a classified ad.
     */
    public function renew(Classified $classified)
    {
        Gate::authorize('update', $classified);

        $classified->update([
            'expires_at' => now()->addDays(30),
            'status' => 'active',
        ]);

        return redirect()->route('classifieds.show', $classified->slug)
            ->with('success', 'Classified ad renewed for 30 more days.');
    }

    /**
     * Toggle bookmark for classified.
     */
    public function toggleBookmark(Classified $classified)
    {
        $isBookmarked = $classified->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }
}
