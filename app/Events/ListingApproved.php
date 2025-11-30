<?php

namespace App\Events;

use App\Models\BusinessListing;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListingApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BusinessListing $listing;

    /**
     * Create a new event instance.
     */
    public function __construct(BusinessListing $listing)
    {
        $this->listing = $listing;
    }
}
