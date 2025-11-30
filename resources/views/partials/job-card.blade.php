<div class="job-card">
    <div class="job-header">
        <h4><a href="{{ route('jobs.show', $job->slug) }}">{{ $job->title }}</a></h4>
        <p class="job-company">{{ $job->user->name }}</p>
    </div>
    <div class="job-details">
        <p class="job-loc">
            <i class="material-icons">location_on</i>
            {{ $job->location->name ?? 'N/A' }}
        </p>
        <p class="job-type">
            <i class="material-icons">work</i>
            {{ ucfirst($job->job_type) }}
        </p>
        @if($job->salary_min || $job->salary_max)
            <p class="job-salary">
                <i class="material-icons">attach_money</i>
                @if($job->salary_min && $job->salary_max)
                    ₹{{ number_format($job->salary_min) }} - ₹{{ number_format($job->salary_max) }}
                @elseif($job->salary_min)
                    ₹{{ number_format($job->salary_min) }}+
                @else
                    Up to ₹{{ number_format($job->salary_max) }}
                @endif
            </p>
        @endif
    </div>
    <div class="job-footer">
        <span class="job-posted">
            <i class="material-icons">schedule</i>
            Posted {{ $job->created_at->diffForHumans() }}
        </span>
        <span class="job-applicants">
            {{ $job->applications_count }} applicants
        </span>
    </div>
</div>
