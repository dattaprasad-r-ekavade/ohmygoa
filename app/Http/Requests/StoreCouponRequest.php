<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canCreateCoupon();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_listing_id' => 'required|exists:business_listings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'required|string|unique:coupons,code|max:50|regex:/^[A-Z0-9]+$/',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_purchase' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_until' => 'required|date|after:valid_from',
            'terms_conditions' => 'nullable|string',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'business_listing_id.required' => 'Please select a business.',
            'title.required' => 'Coupon title is required.',
            'description.required' => 'Please provide a coupon description.',
            'code.required' => 'Coupon code is required.',
            'code.unique' => 'This coupon code is already in use.',
            'code.regex' => 'Coupon code must contain only uppercase letters and numbers.',
            'discount_type.required' => 'Please select discount type.',
            'discount_value.required' => 'Discount value is required.',
            'discount_value.min' => 'Discount value must be a positive number.',
            'valid_from.required' => 'Valid from date is required.',
            'valid_from.after_or_equal' => 'Valid from date must be today or later.',
            'valid_until.required' => 'Valid until date is required.',
            'valid_until.after' => 'Valid until date must be after valid from date.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate percentage discount
            if ($this->discount_type === 'percentage' && $this->discount_value > 100) {
                $validator->errors()->add('discount_value', 'Percentage discount cannot exceed 100%.');
            }

            // Validate maximum discount for percentage type
            if ($this->discount_type === 'percentage' && $this->maximum_discount && $this->maximum_discount <= 0) {
                $validator->errors()->add('maximum_discount', 'Maximum discount must be greater than 0.');
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert code to uppercase
        if ($this->code) {
            $this->merge([
                'code' => strtoupper($this->code),
            ]);
        }
    }
}
