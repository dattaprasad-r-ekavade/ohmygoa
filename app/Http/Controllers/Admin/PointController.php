<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\PointPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display all point transactions
     */
    public function index(Request $request)
    {
        $query = Point::with('user');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
                $q->where('reason', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($uq) use ($request) {
                      $uq->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $points = $query->orderBy('created_at', 'desc')->paginate(50);

        // Statistics
        $stats = [
            'total_credited' => Point::credit()->completed()->sum('amount'),
            'total_debited' => Point::debit()->completed()->sum('amount'),
            'pending_transactions' => Point::where('status', 'pending')->count(),
            'total_users_with_balance' => User::where('points_balance', '>', 0)->count()
        ];

        return view('admin.points.index', compact('points', 'stats'));
    }

    /**
     * Manage point packages
     */
    public function packages()
    {
        $packages = PointPackage::orderBy('display_order')->get();

        return view('admin.points.packages', compact('packages'));
    }

    /**
     * Create new point package
     */
    public function createPackage()
    {
        return view('admin.points.create-package');
    }

    /**
     * Store new point package
     */
    public function storePackage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'points' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'bonus_points' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0'
        ]);

        $package = PointPackage::create($validated);

        return redirect()->route('admin.points.packages')
            ->with('success', 'Point package created successfully!');
    }

    /**
     * Edit point package
     */
    public function editPackage(PointPackage $package)
    {
        return view('admin.points.edit-package', compact('package'));
    }

    /**
     * Update point package
     */
    public function updatePackage(Request $request, PointPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'points' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'bonus_points' => 'nullable|integer|min:0',
            'description' => 'nullable|string|max:500',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer|min:0'
        ]);

        $package->update($validated);

        return redirect()->route('admin.points.packages')
            ->with('success', 'Point package updated successfully!');
    }

    /**
     * Delete point package
     */
    public function destroyPackage(PointPackage $package)
    {
        $package->delete();

        return back()->with('success', 'Point package deleted successfully!');
    }

    /**
     * Manually credit points to user
     */
    public function creditPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($request->user_id);

        Point::addPoints($user, $request->amount, $request->reason);

        return back()->with('success', "Successfully credited {$request->amount} points to {$user->name}");
    }

    /**
     * Manually deduct points from user
     */
    public function debitPoints(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->points_balance < $request->amount) {
            return back()->with('error', 'User has insufficient points balance.');
        }

        Point::deductPoints($user, $request->amount, $request->reason);

        return back()->with('success', "Successfully deducted {$request->amount} points from {$user->name}");
    }

    /**
     * Approve pending point transaction
     */
    public function approveTransaction(Point $point)
    {
        if ($point->status !== 'pending') {
            return back()->with('error', 'Transaction is not pending.');
        }

        DB::beginTransaction();
        try {
            $point->update(['status' => 'completed']);

            // Update user balance
            if ($point->type === 'credit') {
                $point->user->increment('points_balance', $point->amount);
            } else {
                $point->user->decrement('points_balance', $point->amount);
            }

            DB::commit();

            return back()->with('success', 'Transaction approved successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve transaction: ' . $e->getMessage());
        }
    }

    /**
     * Reject pending point transaction
     */
    public function rejectTransaction(Request $request, Point $point)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        if ($point->status !== 'pending') {
            return back()->with('error', 'Transaction is not pending.');
        }

        $point->update([
            'status' => 'failed',
            'notes' => $request->rejection_reason
        ]);

        return back()->with('success', 'Transaction rejected.');
    }

    /**
     * Bulk operations on transactions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'transaction_ids' => 'required|array',
            'transaction_ids.*' => 'exists:points,id'
        ]);

        $points = Point::whereIn('id', $request->transaction_ids)->get();

        DB::beginTransaction();
        try {
            foreach ($points as $point) {
                switch ($request->action) {
                    case 'approve':
                        if ($point->status === 'pending') {
                            $point->update(['status' => 'completed']);
                            if ($point->type === 'credit') {
                                $point->user->increment('points_balance', $point->amount);
                            } else {
                                $point->user->decrement('points_balance', $point->amount);
                            }
                        }
                        break;
                    case 'reject':
                        if ($point->status === 'pending') {
                            $point->update(['status' => 'failed']);
                        }
                        break;
                    case 'delete':
                        $point->delete();
                        break;
                }
            }

            DB::commit();

            return back()->with('success', "Successfully processed {$points->count()} transactions.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    /**
     * Point analytics and reports
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
            'total_credited' => Point::credit()->completed()
                ->where('created_at', '>=', $dateRange)->sum('amount'),
            'total_debited' => Point::debit()->completed()
                ->where('created_at', '>=', $dateRange)->sum('amount'),
            'total_transactions' => Point::where('created_at', '>=', $dateRange)->count(),
            'active_users' => User::where('points_balance', '>', 0)->count(),
            'total_points_in_circulation' => User::sum('points_balance'),
            'package_sales' => DB::table('payments')
                ->where('type', 'point_purchase')
                ->where('status', 'completed')
                ->where('created_at', '>=', $dateRange)
                ->sum('amount'),
            'top_earners' => User::orderBy('points_balance', 'desc')->take(10)->get(),
            'transaction_trends' => Point::selectRaw('DATE(created_at) as date, type, SUM(amount) as total')
                ->where('created_at', '>=', $dateRange)
                ->groupBy('date', 'type')
                ->orderBy('date')
                ->get()
        ];

        return view('admin.points.analytics', compact('analytics', 'period'));
    }
}
