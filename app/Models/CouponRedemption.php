<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'redemption_code',
        'redeemed_at',
        'status',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    /**
     * Coupon relationship.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for redeemed.
     */
    public function scopeRedeemed($query)
    {
        return $query->where('status', 'redeemed');
    }

    /**
     * Scope for pending.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
