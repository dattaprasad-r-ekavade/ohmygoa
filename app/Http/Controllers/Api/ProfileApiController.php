<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    /**
     * Get user's bookmarks.
     */
    public function bookmarks(Request $request)
    {
        $user = $request->user();
        
        $bookmarks = $user->bookmarks()
            ->with(['bookmarkable'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'data' => $bookmarks->map(function ($bookmark) {
                return [
                    'id' => $bookmark->id,
                    'bookmarkable_type' => class_basename($bookmark->bookmarkable_type),
                    'bookmarkable_id' => $bookmark->bookmarkable_id,
                    'bookmarkable' => $bookmark->bookmarkable,
                    'created_at' => $bookmark->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $bookmarks->currentPage(),
                'last_page' => $bookmarks->lastPage(),
                'per_page' => $bookmarks->perPage(),
                'total' => $bookmarks->total(),
            ],
        ]);
    }

    /**
     * Toggle bookmark.
     */
    public function toggleBookmark(Request $request)
    {
        $request->validate([
            'bookmarkable_type' => 'required|string',
            'bookmarkable_id' => 'required|integer',
        ]);

        $user = $request->user();
        $type = 'App\\Models\\' . $request->bookmarkable_type;

        $existing = $user->bookmarks()
            ->where('bookmarkable_type', $type)
            ->where('bookmarkable_id', $request->bookmarkable_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Bookmark removed',
                'bookmarked' => false,
            ]);
        }

        $user->bookmarks()->create([
            'bookmarkable_type' => $type,
            'bookmarkable_id' => $request->bookmarkable_id,
        ]);

        return response()->json([
            'message' => 'Bookmark added',
            'bookmarked' => true,
        ]);
    }

    /**
     * Get user's follows.
     */
    public function follows(Request $request)
    {
        $user = $request->user();
        
        $follows = $user->follows()
            ->with(['followable'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'data' => $follows->map(function ($follow) {
                return [
                    'id' => $follow->id,
                    'followable_type' => class_basename($follow->followable_type),
                    'followable_id' => $follow->followable_id,
                    'followable' => $follow->followable,
                    'created_at' => $follow->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $follows->currentPage(),
                'last_page' => $follows->lastPage(),
                'per_page' => $follows->perPage(),
                'total' => $follows->total(),
            ],
        ]);
    }

    /**
     * Toggle follow.
     */
    public function toggleFollow(Request $request)
    {
        $request->validate([
            'followable_type' => 'required|string',
            'followable_id' => 'required|integer',
        ]);

        $user = $request->user();
        $type = 'App\\Models\\' . $request->followable_type;

        $existing = $user->follows()
            ->where('followable_type', $type)
            ->where('followable_id', $request->followable_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Unfollowed',
                'following' => false,
            ]);
        }

        $user->follows()->create([
            'followable_type' => $type,
            'followable_id' => $request->followable_id,
        ]);

        return response()->json([
            'message' => 'Following',
            'following' => true,
        ]);
    }

    /**
     * Get user's notifications.
     */
    public function notifications(Request $request)
    {
        $user = $request->user();
        
        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 50));

        return response()->json([
            'data' => $notifications->items(),
            'unread_count' => $user->notifications()->whereNull('read_at')->count(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markNotificationRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->findOrFail($id);

        $notification->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllNotificationsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Get wallet balance and transaction history.
     */
    public function wallet(Request $request)
    {
        $user = $request->user();

        $transactions = \DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->whereIn('activity_type', ['payment_received', 'payout_completed', 'commission_earned', 'wallet_debit'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'wallet_balance' => $user->wallet_balance,
            'transactions' => $transactions->items(),
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ],
        ]);
    }

    /**
     * Get points balance and history.
     */
    public function points(Request $request)
    {
        $user = $request->user();

        $history = \DB::table('activity_logs')
            ->where('user_id', $user->id)
            ->whereIn('activity_type', ['points_earned', 'points_spent', 'points_purchased'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'points_balance' => $user->points_balance,
            'history' => $history->items(),
            'meta' => [
                'current_page' => $history->currentPage(),
                'last_page' => $history->lastPage(),
                'per_page' => $history->perPage(),
                'total' => $history->total(),
            ],
        ]);
    }

    /**
     * Purchase points.
     */
    public function purchasePoints(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:point_packages,id',
            'payment_method' => 'required|string',
        ]);

        $user = $request->user();
        $package = \App\Models\PointPackage::findOrFail($request->package_id);

        // Create transaction
        $transaction = \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount' => $package->price,
            'type' => 'points_purchase',
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'description' => "Purchase {$package->points} points",
        ]);

        // In production, integrate with payment gateway
        // For now, auto-approve for development
        $transaction->update(['status' => 'completed']);

        // Add points to user
        $user->increment('points_balance', $package->points);

        // Log activity
        \DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity_type' => 'points_purchased',
            'description' => "Purchased {$package->points} points for ₹{$package->price}",
            'metadata' => json_encode(['package_id' => $package->id, 'transaction_id' => $transaction->id]),
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Points purchased successfully',
            'transaction' => $transaction,
            'points_balance' => $user->points_balance,
        ]);
    }

    /**
     * Redeem points.
     */
    public function redeemPoints(Request $request)
    {
        $request->validate([
            'points' => 'required|integer|min:100',
            'type' => 'required|in:promote_listing,boost_event,feature_job,highlight_product',
            'item_id' => 'required|integer',
        ]);

        $user = $request->user();

        if ($user->points_balance < $request->points) {
            return response()->json([
                'message' => 'Insufficient points balance',
            ], 400);
        }

        // Deduct points
        $user->decrement('points_balance', $request->points);

        // Apply benefit based on type
        switch ($request->type) {
            case 'promote_listing':
                \App\Models\BusinessListing::findOrFail($request->item_id)->update(['is_featured' => true]);
                break;
            case 'boost_event':
                \App\Models\Event::findOrFail($request->item_id)->update(['is_featured' => true]);
                break;
            case 'feature_job':
                \App\Models\JobListing::findOrFail($request->item_id)->update(['is_featured' => true]);
                break;
            case 'highlight_product':
                \App\Models\Product::findOrFail($request->item_id)->update(['is_featured' => true]);
                break;
        }

        // Log activity
        \DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'activity_type' => 'points_spent',
            'description' => "Redeemed {$request->points} points for {$request->type}",
            'metadata' => json_encode(['type' => $request->type, 'item_id' => $request->item_id]),
            'created_at' => now(),
        ]);

        return response()->json([
            'message' => 'Points redeemed successfully',
            'points_balance' => $user->points_balance,
        ]);
    }

    /**
     * Submit a review.
     */
    public function addReview(Request $request)
    {
        $request->validate([
            'reviewable_type' => 'required|string',
            'reviewable_id' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        // Check if already reviewed
        $existing = \App\Models\Review::where('user_id', $user->id)
            ->where('reviewable_type', $request->reviewable_type)
            ->where('reviewable_id', $request->reviewable_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You have already reviewed this item',
            ], 400);
        }

        $review = \App\Models\Review::create([
            'user_id' => $user->id,
            'reviewable_type' => $request->reviewable_type,
            'reviewable_id' => $request->reviewable_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'status' => 'approved', // Auto-approve for now
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review,
        ], 201);
    }

    /**
     * Update a review.
     */
    public function updateReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'review' => 'sometimes|string|max:1000',
        ]);

        $user = $request->user();
        $review = \App\Models\Review::where('user_id', $user->id)->findOrFail($id);

        $review->update($request->only(['rating', 'review']));

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review,
        ]);
    }

    /**
     * Delete a review.
     */
    public function deleteReview($id)
    {
        $user = auth()->user();
        $review = \App\Models\Review::where('user_id', $user->id)->findOrFail($id);

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }

    /**
     * Send an enquiry.
     */
    public function sendEnquiry(Request $request)
    {
        $request->validate([
            'enquirable_type' => 'required|string',
            'enquirable_id' => 'required|integer',
            'message' => 'required|string|max:1000',
        ]);

        $user = $request->user();

        $enquiry = \App\Models\Enquiry::create([
            'user_id' => $user->id,
            'enquirable_type' => $request->enquirable_type,
            'enquirable_id' => $request->enquirable_id,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Enquiry sent successfully',
            'enquiry' => $enquiry,
        ], 201);
    }

    /**
     * Get user's enquiries.
     */
    public function myEnquiries(Request $request)
    {
        $user = $request->user();

        $enquiries = $user->enquiries()
            ->with(['enquirable'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'data' => $enquiries->items(),
            'meta' => [
                'current_page' => $enquiries->currentPage(),
                'last_page' => $enquiries->lastPage(),
                'per_page' => $enquiries->perPage(),
                'total' => $enquiries->total(),
            ],
        ]);
    }

    /**
     * Upload media file.
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:10240',
            'type' => 'required|in:image,document,video',
        ]);

        $user = $request->user();

        $path = $request->file('file')->store('media/' . $request->type . 's', 'public');

        $media = \App\Models\Media::create([
            'user_id' => $user->id,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_type' => $request->type,
            'file_size' => $request->file('file')->getSize(),
            'mime_type' => $request->file('file')->getMimeType(),
        ]);

        return response()->json([
            'message' => 'Media uploaded successfully',
            'media' => [
                'id' => $media->id,
                'url' => asset('storage/' . $path),
                'file_name' => $media->file_name,
                'file_type' => $media->file_type,
            ],
        ], 201);
    }

    /**
     * Delete media file.
     */
    public function deleteMedia($id)
    {
        $user = auth()->user();
        $media = \App\Models\Media::where('user_id', $user->id)->findOrFail($id);

        // Delete file from storage
        \Storage::disk('public')->delete($media->file_path);

        $media->delete();

        return response()->json([
            'message' => 'Media deleted successfully',
        ]);
    }
}
