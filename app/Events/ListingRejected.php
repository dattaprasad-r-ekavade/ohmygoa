<?php

namespace App\Events;

use App\Models\BusinessListing;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BusinessListing $listing;
    public string $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(BusinessListing $listing, string $reason)
    {
        $this->listing = $listing;
        $this->reason = $reason;
    }
}
