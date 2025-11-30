<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_expert_id',
        'user_id',
        'booking_number',
        'service_description',
        'preferred_date',
        'preferred_time',
        'location',
        'contact_name',
        'contact_phone',
        'contact_email',
        'special_instructions',
        'status',
        'quoted_price',
        'expert_notes',
        'confirmed_at',
        'completed_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'quoted_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Service expert relationship.
     */
    public function serviceExpert(): BelongsTo
    {
        return $this->belongsTo(ServiceExpert::class);
    }

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Generate unique booking number.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
            }
        });
    }
}
