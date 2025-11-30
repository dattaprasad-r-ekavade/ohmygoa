<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayoutProcessedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public float $amount;
    public string $transactionId;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, float $amount, string $transactionId = '')
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payout Processed Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payout-processed',
            with: [
                'userName' => $this->user->name,
                'amount' => $this->amount,
                'transactionId' => $this->transactionId,
                'dashboardUrl' => route('dashboard'),
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
