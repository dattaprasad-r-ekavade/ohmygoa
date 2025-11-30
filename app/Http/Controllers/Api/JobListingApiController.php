<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobListingResource;
use App\Models\JobListing;
use Illuminate\Http\Request;

class JobListingApiController extends Controller
{
    /**
     * Get all job listings.
     */
    public function index(Request $request)
    {
        $query = JobListing::where('status', 'active')
            ->with(['user']);

        // Apply filters
        if ($request->has('job_category')) {
            $query->where('job_category', $request->job_category);
        }

        if ($request->has('location_id')) {
            $query->where('job_city', $request->location_id);
        }

        if ($request->has('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_description', 'like', "%{$search}%");
            });
        }

        $jobs = $query->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return JobListingResource::collection($jobs);
    }

    /**
     * Get single job listing.
     */
    public function show($id)
    {
        $job = JobListing::where('status', 'active')
            ->with(['user'])
            ->findOrFail($id);

        $job->increment('view_count');

        return new JobListingResource($job);
    }

    /**
     * Get user's job listings.
     */
    public function myJobs(Request $request)
    {
        $jobs = JobListing::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 20));

        return JobListingResource::collection($jobs);
    }

    /**
     * Create job listing.
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:255',
            'job_description' => 'required|string',
            'job_category' => 'required|string',
            'job_type' => 'required|in:Full-time,Part-time,Contract,Freelance',
            'salary_range' => 'nullable|string',
            'experience_required' => 'nullable|string',
            'job_city' => 'required|exists:locations,id',
            'company_name' => 'required|string',
        ]);

        $job = new JobListing($request->all());
        $job->user_id = $request->user()->id;
        $job->status = 'active';
        $job->slug = \App\Helpers\SlugHelper::generate($request->job_title, JobListing::class);
        $job->save();

        return response()->json([
            'message' => 'Job listing created successfully',
            'job' => new JobListingResource($job),
        ], 201);
    }

    /**
     * Update job listing.
     */
    public function update(Request $request, $id)
    {
        $job = JobListing::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'job_title' => 'sometimes|string|max:255',
            'job_description' => 'sometimes|string',
            'job_type' => 'sometimes|in:Full-time,Part-time,Contract,Freelance',
            'status' => 'sometimes|in:active,closed',
        ]);

        $job->fill($request->all());
        
        if ($request->has('job_title')) {
            $job->slug = \App\Helpers\SlugHelper::generate($request->job_title, JobListing::class, $job->id);
        }

        $job->save();

        return response()->json([
            'message' => 'Job listing updated successfully',
            'job' => new JobListingResource($job),
        ]);
    }

    /**
     * Delete job listing.
     */
    public function destroy(Request $request, $id)
    {
        $job = JobListing::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $job->delete();

        return response()->json([
            'message' => 'Job listing deleted successfully',
        ]);
    }

    /**
     * Apply for a job.
     */
    public function apply(Request $request, $id)
    {
        $job = JobListing::where('status', 'active')->findOrFail($id);

        $request->validate([
            'cover_letter' => 'required|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Check if already applied
        $existing = $job->applications()
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'You have already applied for this job',
            ], 422);
        }

        $resumePath = $request->file('resume')->store('resumes', 'public');

        $application = $job->applications()->create([
            'user_id' => $request->user()->id,
            'cover_letter' => $request->cover_letter,
            'resume_path' => $resumePath,
            'status' => 'pending',
        ]);

        // Dispatch event
        event(new \App\Events\JobApplicationReceived($application));

        return response()->json([
            'message' => 'Job application submitted successfully',
            'application_id' => $application->id,
        ], 201);
    }
}
