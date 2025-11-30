<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $query = Notification::with('user');

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by read status
        if ($request->has('read')) {
            if ($request->read === '1') {
                $query->read();
            } else {
                $query->unread();
            }
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(50);

        // Statistics
        $stats = [
            'total_sent' => Notification::count(),
            'unread' => Notification::unread()->count(),
            'sent_today' => Notification::whereDate('created_at', today())->count(),
            'sent_this_week' => Notification::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
        ];

        return view('admin.notifications.index', compact('notifications', 'stats'));
    }

    /**
     * Create new notification form
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Send notification to users
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_type' => 'required|in:all,role,specific,subscription',
            'recipient_value' => 'required_unless:recipient_type,all',
            'type' => 'required|in:info,success,warning,error,system',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'action_url' => 'nullable|url|max:500',
            'action_text' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100'
        ]);

        // Get recipients
        $recipients = $this->getRecipients(
            $request->recipient_type,
            $request->recipient_value
        );

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No recipients found matching your criteria.');
        }

        // Send notifications
        $sent = Notification::sendToMultiple(
            $recipients->pluck('id')->toArray(),
            $request->type,
            $request->title,
            $request->message,
            $request->action_url,
            $request->action_text,
            $request->icon
        );

        return redirect()->route('admin.notifications.index')
            ->with('success', "Notification sent to {$sent} users successfully!");
    }

    /**
     * Get recipients based on criteria
     */
    private function getRecipients($type, $value)
    {
        return match($type) {
            'all' => User::where('is_active', true)->get(),
            'role' => User::where('role', $value)->where('is_active', true)->get(),
            'specific' => User::whereIn('id', explode(',', $value))->get(),
            'subscription' => User::whereHas('subscription', function($q) use ($value) {
                $q->where('plan_id', $value)->where('status', 'active');
            })->get(),
            default => collect()
        };
    }

    /**
     * Send test notification
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'type' => 'required|in:info,success,warning,error,system',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'action_url' => 'nullable|url|max:500',
            'action_text' => 'nullable|string|max:100'
        ]);

        Notification::send(
            auth()->user(),
            $request->type,
            $request->title . ' [TEST]',
            $request->message,
            $request->action_url,
            $request->action_text
        );

        return back()->with('success', 'Test notification sent to your account');
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return back()->with('success', 'Notification deleted successfully');
    }

    /**
     * Bulk delete notifications
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'notification_ids' => 'required|array',
            'notification_ids.*' => 'exists:notifications,id'
        ]);

        $count = Notification::whereIn('id', $request->notification_ids)->delete();

        return back()->with('success', "Successfully deleted {$count} notifications");
    }

    /**
     * Notification templates
     */
    public function templates()
    {
        $templates = [
            'listing_approved' => [
                'title' => 'Listing Approved',
                'message' => 'Your listing "{listing_title}" has been approved and is now live!',
                'type' => 'success'
            ],
            'listing_rejected' => [
                'title' => 'Listing Rejected',
                'message' => 'Your listing "{listing_title}" was rejected. Reason: {reason}',
                'type' => 'error'
            ],
            'new_enquiry' => [
                'title' => 'New Enquiry',
                'message' => 'You have received a new enquiry for "{listing_title}"',
                'type' => 'info'
            ],
            'new_review' => [
                'title' => 'New Review',
                'message' => '{user_name} left a {rating}-star review on "{listing_title}"',
                'type' => 'info'
            ],
            'subscription_expiring' => [
                'title' => 'Subscription Expiring',
                'message' => 'Your {plan_name} subscription will expire in {days} days',
                'type' => 'warning'
            ],
            'payment_received' => [
                'title' => 'Payment Received',
                'message' => 'Payment of ₹{amount} received for {description}',
                'type' => 'success'
            ],
            'payout_processed' => [
                'title' => 'Payout Processed',
                'message' => 'Your payout of ₹{amount} has been processed',
                'type' => 'success'
            ],
            'job_application' => [
                'title' => 'New Job Application',
                'message' => '{applicant_name} applied for "{job_title}"',
                'type' => 'info'
            ],
            'product_order' => [
                'title' => 'New Product Order',
                'message' => 'New order #{order_id} for "{product_name}"',
                'type' => 'info'
            ]
        ];

        return view('admin.notifications.templates', compact('templates'));
    }

    /**
     * Send notification from template
     */
    public function sendFromTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'variables' => 'nullable|array'
        ]);

        $templates = [
            'listing_approved' => [
                'title' => 'Listing Approved',
                'message' => 'Your listing "{listing_title}" has been approved and is now live!',
                'type' => 'success'
            ],
            // ... other templates
        ];

        if (!isset($templates[$request->template])) {
            return back()->with('error', 'Template not found');
        }

        $template = $templates[$request->template];
        $user = User::findOrFail($request->user_id);

        // Replace variables in message
        $message = $template['message'];
        if ($request->variables) {
            foreach ($request->variables as $key => $value) {
                $message = str_replace('{' . $key . '}', $value, $message);
            }
        }

        Notification::send(
            $user,
            $template['type'],
            $template['title'],
            $message
        );

        return back()->with('success', 'Notification sent successfully');
    }

    /**
     * Notification analytics
     */
    public function analytics(Request $request)
    {
        $period = $request->get('period', '30days');

        $dateRange = match($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30)
        };

        $analytics = [
            'total_sent' => Notification::where('created_at', '>=', $dateRange)->count(),
            'read_rate' => Notification::where('created_at', '>=', $dateRange)
                ->selectRaw('COUNT(*) as total, SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read')
                ->first(),
            'by_type' => Notification::where('created_at', '>=', $dateRange)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'daily_trend' => Notification::where('created_at', '>=', $dateRange)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'top_action_urls' => Notification::where('created_at', '>=', $dateRange)
                ->whereNotNull('action_url')
                ->selectRaw('action_url, COUNT(*) as clicks')
                ->groupBy('action_url')
                ->orderBy('clicks', 'desc')
                ->take(10)
                ->get()
        ];

        return view('admin.notifications.analytics', compact('analytics', 'period'));
    }
}
