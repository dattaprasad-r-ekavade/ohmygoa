<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Mail\ListingApprovedEmail;
use App\Mail\ListingRejectedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of all news.
     */
    public function index(Request $request)
    {
        $query = News::with(['user', 'location'])
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

        $news = $query->paginate(20);
        $pendingCount = News::where('status', 'pending')->count();

        return view('admin.news.index', compact('news', 'pendingCount'));
    }

    /**
     * Display the specified news article.
     */
    public function show(News $news)
    {
        $news->load(['user', 'location']);
        return view('admin.news.show', compact('news'));
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news)
    {
        $categories = News::getCategories();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    /**
     * Update the specified news article in storage.
     */
    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category' => 'required|in:tourism,business,culture,events,general',
            'is_breaking' => 'boolean',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,pending,published,rejected',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $validated['is_breaking'] = $request->boolean('is_breaking');
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($validated['status'] === 'published' && !$news->published_at) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Approve the specified news article.
     */
    public function approve(News $news)
    {
        $news->update([
            'status' => 'published',
            'published_at' => now(),
            'rejection_reason' => null,
        ]);

        // Send approval email (reusing listing approved template)
        // In production, create a dedicated NewsApprovedEmail
        // Mail::to($news->user->email)->queue(new ListingApprovedEmail($news->user, $news));

        return redirect()->back()
            ->with('success', 'News article approved and published.');
    }

    /**
     * Reject the specified news article.
     */
    public function reject(Request $request, News $news)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $news->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Send rejection email (reusing listing rejected template)
        // In production, create a dedicated NewsRejectedEmail
        // Mail::to($news->user->email)->queue(new ListingRejectedEmail($news->user, $news, $validated['rejection_reason']));

        return redirect()->back()
            ->with('success', 'News article rejected.');
    }

    /**
     * Toggle breaking news status.
     */
    public function toggleBreaking(News $news)
    {
        $news->update([
            'is_breaking' => !$news->is_breaking,
        ]);

        $status = $news->is_breaking ? 'marked as breaking' : 'unmarked as breaking';

        return redirect()->back()
            ->with('success', "News article {$status}.");
    }

    /**
     * Toggle featured news status.
     */
    public function toggleFeatured(News $news)
    {
        $news->update([
            'is_featured' => !$news->is_featured,
        ]);

        $status = $news->is_featured ? 'marked as featured' : 'unmarked as featured';

        return redirect()->back()
            ->with('success', "News article {$status}.");
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy(News $news)
    {
        // Delete featured image
        if ($news->featured_image) {
            Storage::disk('public')->delete($news->featured_image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
