<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCreateEvent();
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
            'description' => 'required|string|min:50',
            'short_description' => 'nullable|string|max:500',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'venue' => 'required|string|max:255',
            'venue_address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'organizer_name' => 'required|string|max:255',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'required|string|max:20',
            'ticket_price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'total_seats' => 'nullable|integer|min:1',
            'booking_url' => 'nullable|url',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'category_id.required' => 'Please select an event category.',
            'location_id.required' => 'Please select a location.',
            'description.required' => 'Please provide an event description.',
            'description.min' => 'Description must be at least 50 characters.',
            'start_date.required' => 'Event start date is required.',
            'start_date.after' => 'Event must start in the future.',
            'end_date.after_or_equal' => 'Event end date must be on or after the start date.',
            'venue.required' => 'Venue name is required.',
            'organizer_name.required' => 'Organizer name is required.',
            'organizer_phone.required' => 'Organizer phone number is required.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->is_free && $this->ticket_price > 0) {
                $validator->errors()->add('ticket_price', 'Free events cannot have a ticket price.');
            }
        });
    }
}
