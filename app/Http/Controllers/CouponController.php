<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons.
     */
    public function index(Request $request): View
    {
        $query = Coupon::with(['business'])
            ->active();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Business filter (for admin/specific business)
        if ($request->filled('business_id')) {
            $query->where('business_listing_id', $request->business_id);
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Discount type filter
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        // Valid coupons only
        $query->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $query->orderBy($sortBy, 'desc');

        $coupons = $query->paginate(12);

        return view('coupons.index', [
            'coupons' => $coupons,
        ]);
    }

    /**
     * Display the specified coupon.
     */
    public function show(string $code): View
    {
        $coupon = Coupon::with(['business'])
            ->where('code', $code)
            ->active()
            ->firstOrFail();

        // Check if coupon is valid
        if (!$coupon->isValid()) {
            abort(404, 'This coupon is no longer valid.');
        }

        $coupon->incrementViewCount();

        $hasRedeemed = auth()->check() 
            ? $coupon->redemptions()->where('user_id', auth()->id())->exists()
            : false;

        return view('coupons.show', [
            'coupon' => $coupon,
            'hasRedeemed' => $hasRedeemed,
            'isBookmarked' => auth()->check() ? $coupon->isBookmarkedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create(): View
    {
        Gate::authorize('create-coupon');

        return view('coupons.create');
    }

    /**
     * Store a newly created coupon.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-coupon');

        $validated = $request->validate([
            'business_listing_id' => 'required|exists:business_listings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|unique:coupons,code|max:50',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'terms_conditions' => 'nullable|string',
            'is_featured' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';
        $validated['redemption_count'] = 0;

        // Validate discount percentage
        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Percentage discount cannot exceed 100%']);
        }

        $coupon = Coupon::create($validated);

        return redirect()->route('business.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    /**
     * Show the form for editing the coupon.
     */
    public function edit(Coupon $coupon): View
    {
        Gate::authorize('update', $coupon);

        return view('coupons.edit', [
            'coupon' => $coupon,
        ]);
    }

    /**
     * Update the specified coupon.
     */
    public function update(Request $request, Coupon $coupon)
    {
        Gate::authorize('update', $coupon);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'terms_conditions' => 'nullable|string',
            'status' => 'required|in:active,inactive,expired',
        ]);

        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Percentage discount cannot exceed 100%']);
        }

        $coupon->update($validated);

        return redirect()->route('coupons.show', $coupon->code)
            ->with('success', 'Coupon updated successfully.');
    }

    /**
     * Remove the specified coupon.
     */
    public function destroy(Coupon $coupon)
    {
        Gate::authorize('delete', $coupon);

        $coupon->delete();

        return redirect()->route('business.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    /**
     * Redeem a coupon.
     */
    public function redeem(Request $request, Coupon $coupon)
    {
        if (!$coupon->isValid()) {
            return back()->withErrors(['error' => 'This coupon is no longer valid.']);
        }

        $userId = auth()->id();

        // Check if user has already redeemed
        if ($coupon->usage_per_user) {
            $userRedemptions = $coupon->redemptions()
                ->where('user_id', $userId)
                ->count();

            if ($userRedemptions >= $coupon->usage_per_user) {
                return back()->withErrors(['error' => 'You have reached the maximum usage limit for this coupon.']);
            }
        }

        // Check total usage limit
        if ($coupon->usage_limit && $coupon->redemption_count >= $coupon->usage_limit) {
            return back()->withErrors(['error' => 'This coupon has reached its maximum usage limit.']);
        }

        // Create redemption
        CouponRedemption::create([
            'coupon_id' => $coupon->id,
            'user_id' => $userId,
            'redeemed_at' => now(),
        ]);

        // Increment redemption count
        $coupon->increment('redemption_count');

        // Auto-expire if usage limit reached
        if ($coupon->usage_limit && $coupon->redemption_count >= $coupon->usage_limit) {
            $coupon->update(['status' => 'expired']);
        }

        return redirect()->route('coupons.show', $coupon->code)
            ->with('success', 'Coupon redeemed successfully!');
    }

    /**
     * Validate a coupon code (AJAX).
     */
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'purchase_amount' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $request->code)
            ->active()
            ->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 422);
        }

        // Check minimum purchase
        if ($coupon->minimum_purchase && $request->purchase_amount < $coupon->minimum_purchase) {
            return response()->json([
                'valid' => false,
                'message' => "Minimum purchase of {$coupon->minimum_purchase} required.",
            ], 422);
        }

        // Check usage per user
        if ($coupon->usage_per_user) {
            $userRedemptions = $coupon->redemptions()
                ->where('user_id', auth()->id())
                ->count();

            if ($userRedemptions >= $coupon->usage_per_user) {
                return response()->json([
                    'valid' => false,
                    'message' => 'You have already used this coupon.',
                ], 422);
            }
        }

        // Calculate discount
        $discount = $coupon->discount_type === 'percentage'
            ? ($request->purchase_amount * $coupon->discount_value / 100)
            : $coupon->discount_value;

        if ($coupon->maximum_discount && $discount > $coupon->maximum_discount) {
            $discount = $coupon->maximum_discount;
        }

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'final_amount' => max(0, $request->purchase_amount - $discount),
            'message' => 'Coupon applied successfully!',
        ]);
    }

    /**
     * Toggle bookmark for coupon.
     */
    public function toggleBookmark(Coupon $coupon)
    {
        $isBookmarked = $coupon->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }
}
