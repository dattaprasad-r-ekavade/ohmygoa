<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of published news.
     */
    public function index(Request $request)
    {
        $query = News::published()
            ->with(['user', 'location'])
            ->orderBy('published_at', 'desc');

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $news = $query->paginate(12);
        $breakingNews = News::published()->breaking()->latest('published_at')->take(5)->get();
        $featuredNews = News::published()->featured()->latest('published_at')->take(3)->get();

        return view('news.index', compact('news', 'breakingNews', 'featuredNews'));
    }

    /**
     * Display the specified news article.
     */
    public function show(string $slug)
    {
        $news = News::where('slug', $slug)
            ->published()
            ->with(['user', 'location'])
            ->firstOrFail();

        // Increment view count
        $news->incrementViewCount();

        // Get related news
        $relatedNews = News::published()
            ->where('id', '!=', $news->id)
            ->where('category', $news->category)
            ->latest('published_at')
            ->take(4)
            ->get();

        return view('news.show', compact('news', 'relatedNews'));
    }

    /**
     * Display news by category.
     */
    public function category(string $category)
    {
        if (!array_key_exists($category, News::getCategories())) {
            abort(404);
        }

        $news = News::published()
            ->byCategory($category)
            ->with(['user', 'location'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categoryName = News::getCategories()[$category];

        return view('news.category', compact('news', 'category', 'categoryName'));
    }
}
