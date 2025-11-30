<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Job;
use App\Models\Product;
use App\Models\Event;
use App\Models\Coupon;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    // Listings Management
    public function listings(Request $request)
    {
        $listings = Listing::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->location, fn($q) => $q->where('location_id', $request->location))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                          ->orWhere('description', 'LIKE', "%{$request->search}%");
                });
            })
            ->with(['user', 'category', 'location'])
            ->latest()
            ->paginate(20);

        return view('admin.content.listings', compact('listings'));
    }

    public function updateListing(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,rejected,expired',
            'is_featured' => 'boolean',
            'rejection_reason' => 'nullable|string'
        ]);

        $listing->update($validated);

        return back()->with('success', 'Listing updated successfully!');
    }

    // Jobs Management
    public function jobs(Request $request)
    {
        $jobs = Job::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->location, fn($q) => $q->where('location_id', $request->location))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%")
                          ->orWhere('description', 'LIKE', "%{$request->search}%");
                });
            })
            ->with(['user', 'category', 'location'])
            ->latest()
            ->paginate(20);

        return view('admin.content.jobs', compact('jobs'));
    }

    public function updateJob(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,rejected,expired',
            'is_featured' => 'boolean',
            'rejection_reason' => 'nullable|string'
        ]);

        $job->update($validated);

        return back()->with('success', 'Job updated successfully!');
    }

    // Products Management
    public function products(Request $request)
    {
        $products = Product::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                          ->orWhere('description', 'LIKE', "%{$request->search}%");
                });
            })
            ->with(['user', 'category'])
            ->latest()
            ->paginate(20);

        return view('admin.content.products', compact('products'));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,rejected,out_of_stock',
            'is_featured' => 'boolean',
            'rejection_reason' => 'nullable|string'
        ]);

        $product->update($validated);

        return back()->with('success', 'Product updated successfully!');
    }

    // Events Management
    public function events(Request $request)
    {
        $events = Event::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->location, fn($q) => $q->where('location_id', $request->location))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%")
                          ->orWhere('description', 'LIKE', "%{$request->search}%");
                });
            })
            ->with(['user', 'location'])
            ->latest()
            ->paginate(20);

        return view('admin.content.events', compact('events'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,rejected,cancelled,completed',
            'is_featured' => 'boolean',
            'rejection_reason' => 'nullable|string'
        ]);

        $event->update($validated);

        return back()->with('success', 'Event updated successfully!');
    }

    // Coupons Management
    public function coupons(Request $request)
    {
        $coupons = Coupon::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%")
                          ->orWhere('code', 'LIKE', "%{$request->search}%");
                });
            })
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.content.coupons', compact('coupons'));
    }

    public function updateCoupon(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,rejected,expired',
            'is_featured' => 'boolean',
            'rejection_reason' => 'nullable|string'
        ]);

        $coupon->update($validated);

        return back()->with('success', 'Coupon updated successfully!');
    }

    // Blog Posts Management
    public function blogs(Request $request)
    {
        $blogs = BlogPost::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%")
                          ->orWhere('content', 'LIKE', "%{$request->search}%");
                });
            })
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.content.blogs', compact('blogs'));
    }

    public function updateBlog(Request $request, $id)
    {
        $blog = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:draft,pending,published,rejected',
            'is_featured' => 'boolean',
            'rejection_reason' => 'nullable|string'
        ]);

        $blog->update($validated);

        return back()->with('success', 'Blog post updated successfully!');
    }

    // Bulk Actions
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:listings,jobs,products,events,coupons,blogs',
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'action' => 'required|in:approve,reject,delete,feature,unfeature'
        ]);

        $model = match($validated['type']) {
            'listings' => Listing::class,
            'jobs' => Job::class,
            'products' => Product::class,
            'events' => Event::class,
            'coupons' => Coupon::class,
            'blogs' => BlogPost::class,
        };

        $items = $model::whereIn('id', $validated['ids'])->get();

        foreach ($items as $item) {
            switch ($validated['action']) {
                case 'approve':
                    $item->update(['status' => 'active']);
                    break;
                case 'reject':
                    $item->update(['status' => 'rejected']);
                    break;
                case 'delete':
                    $item->delete();
                    break;
                case 'feature':
                    $item->update(['is_featured' => true]);
                    break;
                case 'unfeature':
                    $item->update(['is_featured' => false]);
                    break;
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }
}
