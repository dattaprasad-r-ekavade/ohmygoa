<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the user's news.
     */
    public function index()
    {
        $news = News::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('business.news.index', compact('news'));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create()
    {
        $categories = News::getCategories();
        $locations = Location::where('type', 'city')->orderBy('name')->get();

        return view('business.news.create', compact('categories', 'locations'));
    }

    /**
     * Store a newly created news article in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'category' => 'required|in:tourism,business,culture,events,general',
            'location_id' => 'nullable|exists:locations,id',
            'source' => 'nullable|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('news', 'public');
        }

        $news = News::create($validated);

        return redirect()->route('business.news.index')
            ->with('success', 'News article submitted for review.');
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news)
    {
        // Ensure user owns this news
        if ($news->user_id !== auth()->id()) {
            abort(403);
        }

        $categories = News::getCategories();
        $locations = Location::where('type', 'city')->orderBy('name')->get();

        return view('business.news.edit', compact('news', 'categories', 'locations'));
    }

    /**
     * Update the specified news article in storage.
     */
    public function update(Request $request, News $news)
    {
        // Ensure user owns this news
        if ($news->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'category' => 'required|in:tourism,business,culture,events,general',
            'location_id' => 'nullable|exists:locations,id',
            'source' => 'nullable|string|max:255',
            'author_name' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Reset status to pending if content changed
        if ($news->status === 'published' && $news->isDirty(['title', 'content'])) {
            $validated['status'] = 'pending';
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($news->featured_image) {
                Storage::disk('public')->delete($news->featured_image);
            }

            $validated['featured_image'] = $request->file('featured_image')
                ->store('news', 'public');
        }

        $news->update($validated);

        return redirect()->route('business.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy(News $news)
    {
        // Ensure user owns this news
        if ($news->user_id !== auth()->id()) {
            abort(403);
        }

        // Delete featured image
        if ($news->featured_image) {
            Storage::disk('public')->delete($news->featured_image);
        }

        $news->delete();

        return redirect()->route('business.news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
