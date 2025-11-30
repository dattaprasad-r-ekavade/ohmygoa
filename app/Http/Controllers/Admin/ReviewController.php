<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::query()
            ->when($request->status, function($q) use ($request) {
                if ($request->status === 'approved') {
                    $q->where('is_approved', true);
                } elseif ($request->status === 'pending') {
                    $q->where('is_approved', false);
                }
            })
            ->when($request->rating, fn($q) => $q->where('rating', $request->rating))
            ->when($request->type, fn($q) => $q->where('reviewable_type', $request->type))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->search}%")
                          ->orWhere('comment', 'LIKE', "%{$request->search}%")
                          ->orWhereHas('user', function($userQuery) use ($request) {
                              $userQuery->where('name', 'LIKE', "%{$request->search}%");
                          });
                });
            })
            ->with(['user', 'reviewable'])
            ->latest()
            ->paginate(20);

        $pendingCount = Review::where('is_approved', false)->count();
        $totalCount = Review::count();
        $averageRating = Review::where('is_approved', true)->avg('rating');

        return view('admin.reviews.index', compact('reviews', 'pendingCount', 'totalCount', 'averageRating'));
    }

    public function show($id)
    {
        $review = Review::with(['user', 'reviewable', 'replyBy'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true, 'status' => 'approved']);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Review approved successfully!']);
        }

        return back()->with('success', 'Review approved successfully!');
    }

    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => false, 'status' => 'rejected']);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Review rejected!']);
        }

        return back()->with('success', 'Review rejected!');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Review deleted successfully!']);
        }

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully!');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
            'action' => 'required|in:approve,reject,delete'
        ]);

        $reviews = Review::whereIn('id', $validated['review_ids'])->get();

        foreach ($reviews as $review) {
            switch ($validated['action']) {
                case 'approve':
                    $review->update(['is_approved' => true]);
                    break;
                case 'reject':
                    $review->update(['is_approved' => false]);
                    break;
                case 'delete':
                    $review->delete();
                    break;
            }
        }

        return back()->with('success', 'Bulk action completed successfully!');
    }

    public function statistics()
    {
        $stats = [
            'total' => Review::count(),
            'approved' => Review::where('is_approved', true)->count(),
            'pending' => Review::where('is_approved', false)->count(),
            'deleted' => Review::onlyTrashed()->count(),
            'today' => Review::whereDate('created_at', today())->count(),
            'this_week' => Review::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => Review::whereMonth('created_at', now()->month)->count(),
        ];

        // Reviews by rating
        $byRating = Review::where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        // Reviews by type
        $byType = Review::where('is_approved', true)
            ->selectRaw('reviewable_type, COUNT(*) as count, AVG(rating) as avg_rating')
            ->groupBy('reviewable_type')
            ->get();

        // Daily reviews (last 30 days)
        $dailyReviews = Review::selectRaw('DATE(created_at) as date, COUNT(*) as count, AVG(rating) as avg_rating')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reviews.statistics', compact('stats', 'byRating', 'byType', 'dailyReviews'));
    }
}
