<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|min:10|max:1000',
            'title' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Please provide a rating.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'comment.min' => 'Review comment must be at least 10 characters.',
            'comment.max' => 'Review comment cannot exceed 1000 characters.',
        ];
    }
}
