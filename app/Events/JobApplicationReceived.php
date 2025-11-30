<?php

namespace App\Events;

use App\Models\JobApplication;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public JobApplication $application;

    /**
     * Create a new event instance.
     */
    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }
}
