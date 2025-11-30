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
     * Mark as read.
     */
    public function markAsRead(): void
    {
        if ($this->status === 'new') {
            $this->update(['status' => 'read']);
        }
    }
}
