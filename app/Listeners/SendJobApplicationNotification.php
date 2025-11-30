<?php

namespace App\Listeners;

use App\Events\JobApplicationReceived;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class SendJobApplicationNotification
{
    protected NotificationService $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(JobApplicationReceived $event): void
    {
        $application = $event->application;
        $jobListing = $application->jobListing;
        $employer = $jobListing->user;
        $applicant = $application->user;

        // Send notification to employer
        $this->notificationService->notifyNewJobApplication($employer, $jobListing, $applicant);

        // Update job stats
        $jobListing->increment('applications_count');

        // Log activity for applicant
        DB::table('activity_logs')->insert([
            'user_id' => $applicant->id,
            'activity_type' => 'job_applied',
            'description' => "Applied for job: {$jobListing->job_title}",
            'reference_type' => 'App\Models\JobApplication',
            'reference_id' => $application->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log activity for employer
        DB::table('activity_logs')->insert([
            'user_id' => $employer->id,
            'activity_type' => 'job_application_received',
            'description' => "New application received for job: {$jobListing->job_title}",
            'reference_type' => 'App\Models\JobApplication',
            'reference_id' => $application->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
