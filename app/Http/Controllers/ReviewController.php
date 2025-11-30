<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewable_type' => 'required|string',
            'reviewable_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', auth()->id())
            ->where('reviewable_type', $validated['reviewable_type'])
            ->where('reviewable_id', $validated['reviewable_id'])
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this item!');
        }

        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $imageUrls[] = $path;
            }
        }

        Review::create([
            'user_id' => auth()->id(),
            'reviewable_type' => $validated['reviewable_type'],
            'reviewable_id' => $validated['reviewable_id'],
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'comment' => $validated['comment'] ?? null,
            'images' => $imageUrls,
            'is_approved' => true // Auto-approve, or set false for moderation
        ]);

        return back()->with('success', 'Review submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review->update($validated);

        return back()->with('success', 'Review updated successfully!');
    }

    public function destroy($id)
    {
        $review = Review::where('user_id', auth()->id())->findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully!');
    }

    public function markHelpful($id)
    {
        $review = Review::findOrFail($id);
        $review->increment('helpful_count');

        return response()->json([
            'success' => true,
            'helpful_count' => $review->helpful_count
        ]);
    }

    public function markNotHelpful($id)
    {
        $review = Review::findOrFail($id);
        $review->increment('not_helpful_count');

        return response()->json([
            'success' => true,
            'not_helpful_count' => $review->not_helpful_count
        ]);
    }

    public function reply(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        // Only business owner or admin can reply
        if ($review->reviewable->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'reply_text' => 'required|string|max:1000'
        ]);

        $review->update([
            'reply_by' => auth()->id(),
            'reply_text' => $validated['reply_text'],
            'reply_at' => now()
        ]);

        return back()->with('success', 'Reply posted successfully!');
    }
}
