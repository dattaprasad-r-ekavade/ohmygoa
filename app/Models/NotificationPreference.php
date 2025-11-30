<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'email_notifications', 'sms_notifications', 'push_notifications',
        'listing_approved', 'listing_rejected', 'new_enquiry', 'new_review',
        'subscription_expiring', 'payment_received', 'payout_processed',
        'new_message', 'job_application', 'product_order', 'marketing_emails'
    ];

    protected $casts = [
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'listing_approved' => 'boolean',
        'listing_rejected' => 'boolean',
        'new_enquiry' => 'boolean',
        'new_review' => 'boolean',
        'subscription_expiring' => 'boolean',
        'payment_received' => 'boolean',
        'payout_processed' => 'boolean',
        'new_message' => 'boolean',
        'job_application' => 'boolean',
        'product_order' => 'boolean',
        'marketing_emails' => 'boolean'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public function isEnabled($notificationType)
    {
        return $this->$notificationType ?? false;
    }

    public function canSendEmail($notificationType)
    {
        return $this->email_notifications && $this->isEnabled($notificationType);
    }

    public function canSendSms($notificationType)
    {
        return $this->sms_notifications && $this->isEnabled($notificationType);
    }

    public function canSendPush($notificationType)
    {
        return $this->push_notifications && $this->isEnabled($notificationType);
    }
}
