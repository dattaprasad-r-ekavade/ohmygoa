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
}
