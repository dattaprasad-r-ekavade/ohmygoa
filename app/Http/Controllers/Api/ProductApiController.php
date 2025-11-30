<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * Get all products.
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 'approved')
            ->with(['user', 'category']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return ProductResource::collection($products);
    }

    /**
     * Get single product.
     */
    public function show($id)
    {
        $product = Product::where('status', 'approved')
            ->with(['user', 'category'])
            ->findOrFail($id);

        $product->increment('view_count');

        return new ProductResource($product);
    }

    /**
     * Get user's products.
     */
    public function myProducts(Request $request)
    {
        $products = Product::where('user_id', $request->user()->id)
            ->with(['category'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return ProductResource::collection($products);
    }

    /**
     * Create product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'condition' => 'required|in:New,Used,Refurbished',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $product = new Product($request->all());
        $product->user_id = $request->user()->id;
        $product->status = 'pending';
        $product->slug = \App\Helpers\SlugHelper::generate($request->title, Product::class);
        
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $product->images = json_encode($images);
        }

        $product->save();

        return response()->json([
            'message' => 'Product created successfully. Pending admin approval.',
            'product' => new ProductResource($product),
        ], 201);
    }

    /**
     * Update product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'condition' => 'sometimes|in:New,Used,Refurbished',
        ]);

        $product->fill($request->all());
        
        if ($request->has('title')) {
            $product->slug = \App\Helpers\SlugHelper::generate($request->title, Product::class, $product->id);
        }

        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Delete product.
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
