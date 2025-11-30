<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponApiController extends Controller
{
    /**
     * Get all coupons.
     */
    public function index(Request $request)
    {
        $query = Coupon::where('status', 'approved')
            ->where('valid_until', '>', now())
            ->with(['user', 'category']);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $coupons = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return CouponResource::collection($coupons);
    }

    /**
     * Get single coupon.
     */
    public function show($id)
    {
        $coupon = Coupon::where('status', 'approved')
            ->where('valid_until', '>', now())
            ->with(['user', 'category'])
            ->findOrFail($id);

        $coupon->increment('view_count');

        return new CouponResource($coupon);
    }

    /**
     * Get user's coupons (created).
     */
    public function myCoupons(Request $request)
    {
        $coupons = Coupon::where('user_id', $request->user()->id)
            ->with(['category'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return CouponResource::collection($coupons);
    }

    /**
     * Get user's purchased coupons.
     */
    public function myPurchasedCoupons(Request $request)
    {
        $redemptions = $request->user()
            ->redemptions()
            ->with(['coupon.category', 'coupon.user'])
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'data' => $redemptions->map(function ($redemption) {
                return [
                    'redemption_id' => $redemption->id,
                    'redemption_code' => $redemption->redemption_code,
                    'redeemed_at' => $redemption->redeemed_at,
                    'status' => $redemption->status,
                    'coupon' => new CouponResource($redemption->coupon),
                ];
            }),
            'meta' => [
                'current_page' => $redemptions->currentPage(),
                'last_page' => $redemptions->lastPage(),
                'per_page' => $redemptions->perPage(),
                'total' => $redemptions->total(),
            ],
        ]);
    }

    /**
     * Create coupon.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',
            'valid_until' => 'required|date|after:today',
            'terms_conditions' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
        ]);

        $coupon = new Coupon($request->all());
        $coupon->user_id = $request->user()->id;
        $coupon->status = 'pending';
        $coupon->slug = \App\Helpers\SlugHelper::generate($request->title, Coupon::class);
        $coupon->coupon_code = strtoupper(\Illuminate\Support\Str::random(8));
        
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('coupons', 'public');
                $images[] = $path;
            }
            $coupon->images = json_encode($images);
        }

        $coupon->save();

        return response()->json([
            'message' => 'Coupon created successfully. Pending admin approval.',
            'coupon' => new CouponResource($coupon),
        ], 201);
    }

    /**
     * Purchase coupon.
     */
    public function purchase(Request $request, $id)
    {
        $coupon = Coupon::where('status', 'approved')
            ->where('valid_until', '>', now())
            ->findOrFail($id);

        // Check if coupon is available
        if ($coupon->quantity_available <= 0) {
            return response()->json([
                'message' => 'Coupon is sold out',
            ], 422);
        }

        // Create payment (in real app, integrate with payment gateway)
        $payment = $request->user()->payments()->create([
            'payment_for' => 'coupon_purchase',
            'payable_type' => Coupon::class,
            'payable_id' => $coupon->id,
            'amount' => $coupon->price,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Please complete the payment',
            'payment_id' => $payment->id,
            'amount' => $coupon->price,
        ]);
    }
}
