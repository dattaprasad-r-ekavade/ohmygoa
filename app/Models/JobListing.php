<?php

namespace App\Models;

use App\Traits\Bookmarkable;
use App\Traits\HasSlug;
use App\Traits\HasViewCount;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListing extends Model
{
    use HasFactory, SoftDeletes, HasSlug, Searchable, Bookmarkable, HasViewCount;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'title',
        'slug',
        'description',
        'responsibilities',
        'requirements',
        'company_name',
        'company_website',
        'company_logo',
        'job_type',
        'experience_level',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'skills',
        'benefits',
        'application_email',
        'application_url',
        'application_deadline',
        'vacancies',
        'applications_count',
        'status',
        'is_featured',
        'is_remote',
        'is_active',
        'views_count',
    ];

    protected $casts = [
        'skills' => 'array',
        'benefits' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'application_deadline' => 'date',
        'is_featured' => 'boolean',
        'is_remote' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $slugSourceColumn = 'title';
    protected $searchable = ['title', 'description', 'company_name'];

    /**
     * Employer relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Category relationship.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Location relationship.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Applications relationship.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * SEO metadata relationship.
     */
    public function seoMetadata(): MorphOne
    {
        return $this->morphOne(SeoMetadata::class, 'seoable');
    }

    /**
     * Scope for active jobs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('status', 'active');
    }

    /**
     * Scope for featured jobs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for remote jobs.
     */
    public function scopeRemote($query)
    {
        return $query->where('is_remote', true);
    }

    /**
     * Check if job is accepting applications.
     */
    public function isAcceptingApplications(): bool
    {
        return $this->status === 'active' &&
               ($this->application_deadline === null || $this->application_deadline->isFuture());
    }

    /**
     * Get salary range display.
     */
    public function getSalaryRangeAttribute(): ?string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return null;
        }

        $min = $this->salary_min ? number_format($this->salary_min) : '';
        $max = $this->salary_max ? number_format($this->salary_max) : '';

        if ($min && $max) {
            return "{$this->salary_currency} {$min} - {$max} / {$this->salary_period}";
        }

        return $min ?: $max;
    }
}
