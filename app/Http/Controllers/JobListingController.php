<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobApplication;
use App\Models\JobListing;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JobListingController extends Controller
{
    /**
     * Display a listing of jobs.
     */
    public function index(Request $request): View
    {
        $query = JobListing::with(['category', 'location', 'user'])
            ->active();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Job type filter
        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }

        // Experience level filter
        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        // Remote filter
        if ($request->filled('remote')) {
            $query->remote();
        }

        // Featured filter
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Salary range filter
        if ($request->filled('min_salary')) {
            $query->where('salary_min', '>=', $request->min_salary);
        }

        if ($request->filled('max_salary')) {
            $query->where('salary_max', '<=', $request->max_salary);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $query->orderBy($sortBy, 'desc');

        $jobs = $query->paginate(15);

        return view('jobs.index', [
            'jobs' => $jobs,
            'categories' => Category::active()->ofType('job')->get(),
            'locations' => Location::active()->popular()->get(),
        ]);
    }

    /**
     * Display the specified job.
     */
    public function show(string $slug): View
    {
        $job = JobListing::with(['category', 'location', 'user'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $job->incrementViewCount();

        // Similar jobs
        $similarJobs = JobListing::where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->active()
            ->limit(5)
            ->get();

        $hasApplied = auth()->check() 
            ? $job->applications()->where('user_id', auth()->id())->exists()
            : false;

        return view('jobs.show', [
            'job' => $job,
            'similarJobs' => $similarJobs,
            'hasApplied' => $hasApplied,
            'isBookmarked' => auth()->check() ? $job->isBookmarkedBy(auth()->id()) : false,
        ]);
    }

    /**
     * Show the form for creating a new job.
     */
    public function create(): View
    {
        Gate::authorize('create-job');

        return view('jobs.create', [
            'categories' => Category::active()->ofType('job')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Store a newly created job.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-job');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'company_name' => 'required|string',
            'company_website' => 'nullable|url',
            'company_logo' => 'nullable|image|max:2048',
            'job_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'experience_level' => 'required|in:entry,mid,senior,lead',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string',
            'salary_period' => 'nullable|in:hourly,monthly,yearly',
            'skills' => 'nullable|array',
            'benefits' => 'nullable|array',
            'application_email' => 'nullable|email',
            'application_url' => 'nullable|url',
            'application_deadline' => 'nullable|date|after:today',
            'vacancies' => 'required|integer|min:1',
            'is_remote' => 'required|boolean',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'active';

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('jobs/logos', 'public');
        }

        $job = JobListing::create($validated);

        return redirect()->route('jobs.show', $job->slug)
            ->with('success', 'Job posted successfully.');
    }

    /**
     * Show the form for editing the job.
     */
    public function edit(JobListing $job): View
    {
        Gate::authorize('update', $job);

        return view('jobs.edit', [
            'job' => $job,
            'categories' => Category::active()->ofType('job')->get(),
            'locations' => Location::active()->cities()->get(),
        ]);
    }

    /**
     * Update the specified job.
     */
    public function update(Request $request, JobListing $job)
    {
        Gate::authorize('update', $job);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'company_name' => 'required|string',
            'company_website' => 'nullable|url',
            'company_logo' => 'nullable|image|max:2048',
            'job_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'experience_level' => 'required|in:entry,mid,senior,lead',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'application_deadline' => 'nullable|date',
            'vacancies' => 'required|integer|min:1',
            'is_remote' => 'required|boolean',
            'status' => 'required|in:draft,active,closed,filled',
        ]);

        if ($request->hasFile('company_logo')) {
            $validated['company_logo'] = $request->file('company_logo')->store('jobs/logos', 'public');
        }

        $job->update($validated);

        return redirect()->route('jobs.show', $job->slug)
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job.
     */
    public function destroy(JobListing $job)
    {
        Gate::authorize('delete', $job);

        $job->delete();

        return redirect()->route('jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    /**
     * Show the form for applying to a job.
     */
    public function apply(JobListing $job): View
    {
        if (!$job->isAcceptingApplications()) {
            abort(403, 'This job is no longer accepting applications.');
        }

        if ($job->applications()->where('user_id', auth()->id())->exists()) {
            abort(403, 'You have already applied to this job.');
        }

        return view('jobs.apply', ['job' => $job]);
    }

    /**
     * Store a job application.
     */
    public function storeApplication(Request $request, JobListing $job)
    {
        if (!$job->isAcceptingApplications()) {
            return back()->withErrors(['error' => 'This job is no longer accepting applications.']);
        }

        if ($job->applications()->where('user_id', auth()->id())->exists()) {
            return back()->withErrors(['error' => 'You have already applied to this job.']);
        }

        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email',
            'applicant_phone' => 'nullable|string|max:20',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['job_listing_id'] = $job->id;
        $validated['status'] = 'pending';

        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('applications/resumes', 'public');
        }

        $job->applications()->create($validated);
        $job->increment('applications_count');

        return redirect()->route('jobs.show', $job->slug)
            ->with('success', 'Application submitted successfully.');
    }

    /**
     * Toggle bookmark for job.
     */
    public function toggleBookmark(JobListing $job)
    {
        $isBookmarked = $job->toggleBookmark(auth()->id());

        return response()->json([
            'bookmarked' => $isBookmarked,
            'message' => $isBookmarked ? 'Added to bookmarks' : 'Removed from bookmarks',
        ]);
    }
}
