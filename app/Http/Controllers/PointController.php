<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\PointPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's point history
     */
    public function index(Request $request)
    {
        $query = Auth::user()->points();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by reason
        if ($request->filled('search')) {
            $query->where('reason', 'like', '%' . $request->search . '%');
        }

        $points = $query->orderBy('created_at', 'desc')->paginate(20);
        $balance = Auth::user()->points_balance;

        // Statistics
        $stats = [
            'total_earned' => Auth::user()->points()->credit()->completed()->sum('amount'),
            'total_spent' => Auth::user()->points()->debit()->completed()->sum('amount'),
            'current_balance' => $balance,
            'pending' => Auth::user()->points()->where('status', 'pending')->sum('amount')
        ];

        return view('points.index', compact('points', 'balance', 'stats'));
    }

    /**
     * Show point packages for purchase
     */
    public function packages()
    {
        $packages = PointPackage::active()->ordered()->get();
        $balance = Auth::user()->points_balance;

        return view('points.packages', compact('packages', 'balance'));
    }

    /**
     * Purchase point package
     */
    public function purchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:point_packages,id',
            'payment_method' => 'required|in:razorpay,stripe,paypal,bank_transfer'
        ]);

        $package = PointPackage::active()->findOrFail($request->package_id);

        DB::beginTransaction();
        try {
            // Create payment record (simplified - integrate with payment gateway)
            $payment = Auth::user()->payments()->create([
                'amount' => $package->price,
                'currency' => 'INR',
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'type' => 'point_purchase',
                'reference_type' => PointPackage::class,
                'reference_id' => $package->id,
                'description' => "Purchase of {$package->name}",
                'metadata' => [
                    'package_id' => $package->id,
                    'points' => $package->points,
                    'bonus_points' => $package->bonus_points,
                    'total_points' => $package->total_points
                ]
            ]);

            // In production, redirect to payment gateway
            // For now, simulate successful payment
            if ($request->payment_method === 'bank_transfer') {
                // Manual approval required
                DB::commit();
                return redirect()->route('points.index')
                    ->with('info', 'Payment request submitted. Points will be credited after bank transfer verification.');
            }

            // Auto-credit for other methods (after gateway callback in production)
            $payment->update([
                'status' => 'completed',
                'transaction_id' => 'SIM_' . uniqid(),
                'paid_at' => now()
            ]);

            // Credit points
            Point::addPoints(
                Auth::user(),
                $package->total_points,
                "Purchased {$package->name}",
                $payment
            );

            DB::commit();

            return redirect()->route('points.index')
                ->with('success', "Successfully purchased {$package->total_points} points!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Purchase failed: ' . $e->getMessage());
        }
    }

    /**
     * Redeem points for listing promotion
     */
    public function redeem(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'promotion_type' => 'required|in:featured,urgent,highlight,top_listing',
            'duration_days' => 'required|integer|min:1|max:90'
        ]);

        $listing = Auth::user()->listings()->findOrFail($request->listing_id);

        // Calculate points required
        $pointsRequired = $this->calculatePromotionPoints(
            $request->promotion_type,
            $request->duration_days
        );

        if (Auth::user()->points_balance < $pointsRequired) {
            return back()->with('error', "Insufficient points. You need {$pointsRequired} points but have " . Auth::user()->points_balance);
        }

        DB::beginTransaction();
        try {
            // Deduct points
            Point::deductPoints(
                Auth::user(),
                $pointsRequired,
                "Redeemed for {$request->promotion_type} promotion on listing: {$listing->title}",
                $listing
            );

            // Apply promotion
            $listing->promotions()->create([
                'type' => $request->promotion_type,
                'start_date' => now(),
                'end_date' => now()->addDays($request->duration_days),
                'status' => 'active',
                'points_used' => $pointsRequired
            ]);

            DB::commit();

            return redirect()->route('listings.show', $listing)
                ->with('success', 'Promotion applied successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Redemption failed: ' . $e->getMessage());
        }
    }

    /**
     * Calculate points required for promotion
     */
    private function calculatePromotionPoints($type, $days)
    {
        $basePoints = [
            'featured' => 50,
            'urgent' => 30,
            'highlight' => 20,
            'top_listing' => 100
        ];

        return ($basePoints[$type] ?? 50) * $days;
    }

    /**
     * Transfer points to another user
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'amount' => 'required|integer|min:10',
            'note' => 'nullable|string|max:255'
        ]);

        $recipient = \App\Models\User::where('email', $request->recipient_email)->first();

        if ($recipient->id === Auth::id()) {
            return back()->with('error', 'Cannot transfer points to yourself.');
        }

        if (Auth::user()->points_balance < $request->amount) {
            return back()->with('error', 'Insufficient points balance.');
        }

        DB::beginTransaction();
        try {
            // Deduct from sender
            Point::deductPoints(
                Auth::user(),
                $request->amount,
                "Transferred to {$recipient->name}" . ($request->note ? ": {$request->note}" : ''),
                $recipient
            );

            // Credit to recipient
            Point::addPoints(
                $recipient,
                $request->amount,
                "Received from " . Auth::user()->name . ($request->note ? ": {$request->note}" : ''),
                Auth::user()
            );

            DB::commit();

            return back()->with('success', "Successfully transferred {$request->amount} points to {$recipient->name}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }
}
