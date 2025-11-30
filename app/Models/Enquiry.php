<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'enquirable_type',
        'enquirable_id',
        'name',
        'email',
        'phone',
        'message',
        'status',
        'admin_notes',
        'read_at',
        'replied_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime'
    ];

    /**
     * Enquirable relationship (polymorphic).
     */
    public function enquirable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * User relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for new enquiries.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope for read enquiries.
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope for replied enquiries.
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Scope for closed enquiries.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Mark as read.
     */
    public function markAsRead(): void
    {
        if ($this->status === 'new') {
            $this->update([
                'status' => 'read',
                'read_at' => now()
            ]);
        }
    }

    /**
     * Mark as replied.
     */
    public function markAsReplied()
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now()
        ]);
    }

    /**
     * Close enquiry.
     */
    public function close()
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Get all statuses.
     */
    public static function getStatuses()
    {
        return ['new', 'read', 'replied', 'closed'];
    }
}
