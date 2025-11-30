<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Payout;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    // Payments Management
    public function payments(Request $request)
    {
        $payments = Payment::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->type, fn($q) => $q->where('payment_type', $request->type))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->search, function($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('transaction_id', 'LIKE', "%{$request->search}%")
                          ->orWhereHas('user', function($userQuery) use ($request) {
                              $userQuery->where('name', 'LIKE', "%{$request->search}%")
                                       ->orWhere('email', 'LIKE', "%{$request->search}%");
                          });
                });
            })
            ->with(['user', 'payable'])
            ->latest()
            ->paginate(20);

        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        return view('admin.financial.payments', compact('payments', 'totalRevenue', 'monthlyRevenue'));
    }

    public function paymentDetails($id)
    {
        $payment = Payment::with(['user', 'payable'])->findOrFail($id);
        return view('admin.financial.payment-details', compact('payment'));
    }

    public function refundPayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'refund_reason' => 'required|string|max:500'
        ]);

        $payment->update([
            'status' => 'refunded',
            'refund_reason' => $validated['refund_reason'],
            'refunded_at' => now()
        ]);

        return back()->with('success', 'Payment refunded successfully!');
    }

    // Commission Management
    public function commissions(Request $request)
    {
        $commissions = Payment::where('commission_amount', '>', 0)
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->with(['user', 'payable'])
            ->latest()
            ->paginate(20);

        $totalCommission = Payment::where('status', 'completed')
            ->sum('commission_amount');
        
        $monthlyCommission = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('commission_amount');

        return view('admin.financial.commissions', compact('commissions', 'totalCommission', 'monthlyCommission'));
    }

    // Payout Management
    public function payouts(Request $request)
    {
        $payouts = Payout::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->with('user')
            ->latest()
            ->paginate(20);

        $pendingAmount = Payout::where('status', 'pending')->sum('amount');
        $paidAmount = Payout::where('status', 'paid')->sum('amount');

        return view('admin.financial.payouts', compact('payouts', 'pendingAmount', 'paidAmount'));
    }

    public function approvePayout($id)
    {
        $payout = Payout::findOrFail($id);

        if ($payout->status !== 'pending') {
            return back()->with('error', 'Only pending payouts can be approved!');
        }

        $payout->update([
            'status' => 'processing',
            'approved_at' => now()
        ]);

        return back()->with('success', 'Payout approved and processing!');
    }

    public function markPayoutAsPaid(Request $request, $id)
    {
        $payout = Payout::findOrFail($id);

        $validated = $request->validate([
            'transaction_reference' => 'required|string|max:255',
            'payment_method' => 'required|string|max:50',
            'admin_notes' => 'nullable|string'
        ]);

        $payout->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_reference' => $validated['transaction_reference'],
            'payment_method' => $validated['payment_method'],
            'admin_notes' => $validated['admin_notes'] ?? null
        ]);

        return back()->with('success', 'Payout marked as paid!');
    }

    public function rejectPayout(Request $request, $id)
    {
        $payout = Payout::findOrFail($id);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $payout->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason']
        ]);

        // Return amount to user's balance
        $user = $payout->user;
        $user->increment('balance', $payout->amount);

        return back()->with('success', 'Payout rejected and amount returned to user balance!');
    }

    // Subscriptions Management
    public function subscriptions(Request $request)
    {
        $subscriptions = Subscription::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->plan, fn($q) => $q->where('plan_type', $request->plan))
            ->when($request->search, function($q) use ($request) {
                $q->whereHas('user', function($userQuery) use ($request) {
                    $userQuery->where('name', 'LIKE', "%{$request->search}%")
                             ->orWhere('email', 'LIKE', "%{$request->search}%");
                });
            })
            ->with('user')
            ->latest()
            ->paginate(20);

        $activeCount = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
        
        $expiringCount = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        return view('admin.financial.subscriptions', compact('subscriptions', 'activeCount', 'expiringCount'));
    }

    public function extendSubscription(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $validated = $request->validate([
            'extend_days' => 'required|integer|min:1|max:365',
            'reason' => 'nullable|string'
        ]);

        $subscription->update([
            'expires_at' => $subscription->expires_at->addDays($validated['extend_days']),
            'admin_notes' => $validated['reason'] ?? null
        ]);

        return back()->with('success', "Subscription extended by {$validated['extend_days']} days!");
    }

    public function cancelSubscription(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason']
        ]);

        return back()->with('success', 'Subscription cancelled!');
    }

    // Invoices
    public function invoices(Request $request)
    {
        $invoices = Invoice::query()
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, function($q) use ($request) {
                $q->where('invoice_number', 'LIKE', "%{$request->search}%")
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'LIKE', "%{$request->search}%");
                  });
            })
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('admin.financial.invoices', compact('invoices'));
    }

    // Revenue Analytics
    public function revenueAnalytics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->subMonths(12)->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        // Revenue by month
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Revenue by type
        $revenueByType = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('payment_type, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_type')
            ->get();

        // Top paying users
        $topUsers = User::withSum(['payments' => function($query) use ($dateFrom, $dateTo) {
                $query->where('status', 'completed')
                      ->whereBetween('created_at', [$dateFrom, $dateTo]);
            }], 'amount')
            ->having('payments_sum_amount', '>', 0)
            ->orderBy('payments_sum_amount', 'desc')
            ->limit(10)
            ->get();

        $totalRevenue = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('amount');

        $totalCommission = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('commission_amount');

        return view('admin.financial.revenue-analytics', compact(
            'monthlyRevenue', 'revenueByType', 'topUsers', 'totalRevenue', 'totalCommission'
        ));
    }
}
