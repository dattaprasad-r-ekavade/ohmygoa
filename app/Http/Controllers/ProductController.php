<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'user'])
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

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Stock status filter
        if ($request->filled('in_stock')) {
            $query->inStock();
        }

        // On sale filter
        if ($request->filled('on_sale')) {
            $query->onSale();
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
        } elseif ($sortBy === 'rating') {
            $query->orderBy('average_rating', $sortOrder);
        } elseif ($sortBy === 'popular') {
            $query->orderBy('views_count', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $products = $query->paginate(16);

        return view('products.index', [
            'products' => $products,
            'categories' => Category::active()->ofType('product')->get(),
            'brands' => Product::distinct()->pluck('brand')->filter()->sort(),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug): View
    {
        $product = Product::with(['category', 'user'])
            ->where('slug', $slug)
            ->active()
            ->approved()
            ->firstOrFail();

        $product->incrementViewCount();

        // Load reviews with pagination
        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        // Related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->approved()
            ->limit(4)
            ->get();

        return view('products.show', [
            'product' => $product,
            'reviews' => $reviews,
            'relatedProducts' => $relatedProducts,
            'isBookmarked' => auth()->check() ? $product->isBookmarkedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        Gate::authorize('create-product');

        return view('products.create', [
            'categories' => Category::active()->ofType('product')->get(),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-product');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'main_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['stock_status'] = $validated['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        if ($request->hasFile('gallery')) {
            $gallery = [];
            foreach ($request->file('gallery') as $image) {
                $gallery[] = $image->store('products/gallery', 'public');
            }
            $validated['gallery'] = $gallery;
        }

        $product = Product::create($validated);

        return redirect()->route('business.products.index')
            ->with('success', 'Product created successfully and is pending approval.');
    }

    /**
     * Show the form for editing the product.
     */
    public function edit(Product $product): View
    {
        Gate::authorize('update', $product);

        return view('products.edit', [
            'product' => $product,
            'categories' => Category::active()->ofType('product')->get(),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        Gate::authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'brand' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'specifications' => 'nullable|array',
            'main_image' => 'nullable|image|max:4096',
        ]);

        $validated['stock_status'] = $validated['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.show', $product->slug)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        Gate::authorize('delete', $product);

        $product->delete();

        return redirect()->route('business.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle bookmark for product.
     */
    public function toggleBookmark(Product $product)
    {
        $isBookmarked = $product->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }
}
