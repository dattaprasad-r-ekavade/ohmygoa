<?php

namespace App\Mail;

use App\Models\BusinessListing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ListingRejectedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public BusinessListing $listing;
    public string $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, BusinessListing $listing, string $reason)
    {
        $this->user = $user;
        $this->listing = $listing;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Listing Requires Attention',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.listing-rejected',
            with: [
                'userName' => $this->user->name,
                'listing' => $this->listing,
                'reason' => $this->reason,
                'editUrl' => route('business.listings.edit', $this->listing->id),
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
