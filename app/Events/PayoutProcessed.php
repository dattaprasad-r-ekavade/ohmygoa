<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayoutProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public float $amount;
    public string $status;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, float $amount, string $status)
    {
        $this->user = $user;
        $this->amount = $amount;
        $this->status = $status;
    }
}
