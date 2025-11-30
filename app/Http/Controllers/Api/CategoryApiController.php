<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryApiController extends Controller
{
    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        $type = $request->get('type'); // business, event, job, product, service, classified
        $parentOnly = $request->get('parent_only', false);

        $query = Category::where('is_active', true)->orderBy('display_order');

        if ($type) {
            $query->where('type', $type);
        }

        if ($parentOnly) {
            $query->whereNull('parent_id');
        }

        $categories = $query->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'type' => $category->type,
                'icon' => $category->icon,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
                'description' => $category->description,
                'is_featured' => $category->is_featured,
                'parent_id' => $category->parent_id,
                'listings_count' => $category->listings()->count(),
                'children_count' => $category->children()->count(),
            ];
        });

        return response()->json([
            'categories' => $categories,
            'total' => $categories->count(),
        ]);
    }

    /**
     * Get single category
     */
    public function show($id)
    {
        $category = Category::where('is_active', true)->findOrFail($id);

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'type' => $category->type,
                'icon' => $category->icon,
                'image' => $category->image ? asset('storage/' . $category->image) : null,
                'description' => $category->description,
                'is_featured' => $category->is_featured,
                'parent_id' => $category->parent_id,
                'parent' => $category->parent ? [
                    'id' => $category->parent->id,
                    'name' => $category->parent->name,
                    'slug' => $category->parent->slug,
                ] : null,
                'children' => $category->children()->where('is_active', true)->get()->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                    ];
                }),
                'listings_count' => $category->listings()->where('status', 'approved')->count(),
                'meta_title' => $category->meta_title,
                'meta_description' => $category->meta_description,
            ],
        ]);
    }

    /**
     * Get listings in category
     */
    public function listings($id, Request $request)
    {
        $category = Category::where('is_active', true)->findOrFail($id);
        $limit = $request->get('limit', 20);

        $listings = $category->listings()
            ->where('status', 'approved')
            ->where('is_active', true)
            ->with(['location', 'user'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
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
