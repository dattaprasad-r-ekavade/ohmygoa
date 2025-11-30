<?php

namespace App\Mail;

use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationReceivedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $employer;
    public JobListing $job;
    public User $applicant;

    /**
     * Create a new message instance.
     */
    public function __construct(User $employer, JobListing $job, User $applicant)
    {
        $this->employer = $employer;
        $this->job = $job;
        $this->applicant = $applicant;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Job Application for {$this->job->job_title}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application-received',
            with: [
                'employerName' => $this->employer->name,
                'jobTitle' => $this->job->job_title,
                'applicantName' => $this->applicant->name,
                'applicantEmail' => $this->applicant->email,
                'applicantPhone' => $this->applicant->phone,
                'jobUrl' => route('jobs.show', $this->job->slug),
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
