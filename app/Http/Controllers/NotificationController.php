<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's notifications
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by read/unread
        if ($request->has('read')) {
            if ($request->read === '1') {
                $query->read();
            } else {
                $query->unread();
            }
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        $unreadCount = Auth::user()->notifications()->unread()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification deleted');
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        Auth::user()->notifications()->read()->delete();

        return back()->with('success', 'All read notifications deleted');
    }

    /**
     * Get unread notification count
     */
    public function unreadCount()
    {
        $count = Auth::user()->notifications()->unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Display notification preferences
     */
    public function preferences()
    {
        $preferences = Auth::user()->notificationPreference 
            ?? Auth::user()->notificationPreference()->create();

        return view('notifications.preferences', compact('preferences'));
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'listing_approved' => 'boolean',
            'listing_rejected' => 'boolean',
            'new_enquiry' => 'boolean',
            'new_review' => 'boolean',
            'subscription_expiring' => 'boolean',
            'payment_received' => 'boolean',
            'payout_processed' => 'boolean',
            'new_message' => 'boolean',
            'job_application' => 'boolean',
            'product_order' => 'boolean',
            'marketing_emails' => 'boolean'
        ]);

        $preferences = Auth::user()->notificationPreference 
            ?? Auth::user()->notificationPreference()->create();

        $preferences->update($validated);

        return back()->with('success', 'Notification preferences updated successfully');
    }

    /**
     * Get recent notifications for dropdown/widget
     */
    public function recent()
    {
        $notifications = Auth::user()->notifications()
            ->recent()
            ->take(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->notifications()->unread()->count()
        ]);
    }
}
