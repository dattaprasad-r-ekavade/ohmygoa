<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public int $daysRemaining;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, int $daysRemaining)
    {
        $this->user = $user;
        $this->daysRemaining = $daysRemaining;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your Premium Subscription Expires in {$this->daysRemaining} Days",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.subscription-expiring',
            with: [
                'userName' => $this->user->name,
                'daysRemaining' => $this->daysRemaining,
                'expiryDate' => $this->user->subscription_ends_at?->format('d M Y'),
                'renewUrl' => route('dashboard'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
