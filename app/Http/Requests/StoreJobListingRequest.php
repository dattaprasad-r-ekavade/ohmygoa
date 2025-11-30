<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobListingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCreateJob();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'description' => 'required|string|min:100',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'company_name' => 'required|string|max:255',
            'company_website' => 'nullable|url',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'job_type' => 'required|in:full-time,part-time,contract,freelance,internship',
            'experience_level' => 'required|in:entry,mid,senior,lead',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0|gte:salary_min',
            'salary_currency' => 'nullable|string|size:3',
            'salary_period' => 'nullable|in:hourly,monthly,yearly',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:100',
            'benefits' => 'nullable|array',
            'benefits.*' => 'string|max:255',
            'application_email' => 'nullable|email',
            'application_url' => 'nullable|url',
            'application_deadline' => 'nullable|date|after:today',
            'vacancies' => 'required|integer|min:1|max:999',
            'is_remote' => 'required|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Job title is required.',
            'category_id.required' => 'Please select a job category.',
            'location_id.required' => 'Please select a location.',
            'description.required' => 'Please provide a job description.',
            'description.min' => 'Job description must be at least 100 characters.',
            'company_name.required' => 'Company name is required.',
            'job_type.required' => 'Please select a job type.',
            'experience_level.required' => 'Please select required experience level.',
            'salary_max.gte' => 'Maximum salary must be greater than or equal to minimum salary.',
            'vacancies.required' => 'Number of vacancies is required.',
            'vacancies.max' => 'Number of vacancies cannot exceed 999.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->application_email && !$this->application_url) {
                $validator->errors()->add('application_email', 'Either application email or URL is required.');
            }
        });
    }
}
