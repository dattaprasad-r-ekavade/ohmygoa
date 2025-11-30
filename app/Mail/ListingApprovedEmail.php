<?php

namespace App\Mail;

use App\Models\BusinessListing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ListingApprovedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public BusinessListing $listing;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, BusinessListing $listing)
    {
        $this->user = $user;
        $this->listing = $listing;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Listing Has Been Approved!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.listing-approved',
            with: [
                'userName' => $this->user->name,
                'listing' => $this->listing,
                'listingUrl' => route('listings.show', $this->listing->slug),
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
