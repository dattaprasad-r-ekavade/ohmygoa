@php
    $listValue = function (string $field) use ($expert) {
        $oldValue = old($field);
        if (!is_null($oldValue)) {
            return is_array($oldValue) ? implode("\n", $oldValue) : $oldValue;
        }

        $value = optional($expert)->{$field} ?? '';
        return is_array($value) ? implode("\n", $value) : $value;
    };

    $daysOfWeek = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    $workingHoursInput = old('working_hours');
    if (is_null($workingHoursInput)) {
        $workingHoursInput = optional($expert)->working_hours ?? [];
    }
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" class="card shadow-sm p-4 mb-4">
    @csrf
    @if(strtoupper($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="business_name">Business / Expert Name</label>
            <input type="text" class="form-control @error('business_name') is-invalid @enderror" id="business_name" name="business_name" value="{{ old('business_name', optional($expert)->business_name) }}" required>
            @error('business_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-3">
            <label for="category_id">Category</label>
            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                <option value="">Select category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (int) old('category_id', optional($expert)->category_id) === $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-3">
            <label for="location_id">Location</label>
            <select class="form-control @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                <option value="">Select location</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ (int) old('location_id', optional($expert)->location_id) === $location->id ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
            @error('location_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="years_of_experience">Years of Experience</label>
            <input type="number" min="0" class="form-control @error('years_of_experience') is-invalid @enderror" id="years_of_experience" name="years_of_experience" value="{{ old('years_of_experience', optional($expert)->years_of_experience) }}" required>
            @error('years_of_experience')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="hourly_rate">Hourly Rate (₹)</label>
            <input type="number" step="0.01" min="0" class="form-control @error('hourly_rate') is-invalid @enderror" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', optional($expert)->hourly_rate) }}">
            @error('hourly_rate')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="minimum_charge">Minimum Charge (₹)</label>
            <input type="number" step="0.01" min="0" class="form-control @error('minimum_charge') is-invalid @enderror" id="minimum_charge" name="minimum_charge" value="{{ old('minimum_charge', optional($expert)->minimum_charge) }}">
            @error('minimum_charge')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="description">Overview</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description', optional($expert)->description) }}</textarea>
        @error('description')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="services_offered">Services Offered <small class="text-muted">(comma or line separated)</small></label>
            <textarea class="form-control @error('services_offered') is-invalid @enderror" id="services_offered" name="services_offered" rows="4" required>{{ $listValue('services_offered') }}</textarea>
            @error('services_offered')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-6">
            <label for="service_areas">Service Areas <small class="text-muted">(comma or line separated)</small></label>
            <textarea class="form-control @error('service_areas') is-invalid @enderror" id="service_areas" name="service_areas" rows="4">{{ $listValue('service_areas') }}</textarea>
            @error('service_areas')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="contact_phone">Contact Phone</label>
            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', optional($expert)->contact_phone) }}" required>
            @error('contact_phone')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="contact_email">Contact Email</label>
            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', optional($expert)->contact_email) }}">
            @error('contact_email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="website">Website</label>
            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', optional($expert)->website) }}">
            @error('website')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="address">Address</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', optional($expert)->address) }}">
            @error('address')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-6">
            <label for="availability">Availability Notes</label>
            <input type="text" class="form-control @error('availability') is-invalid @enderror" id="availability" name="availability" value="{{ old('availability', optional($expert)->availability) }}">
            @error('availability')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="languages_spoken">Languages Spoken</label>
            <textarea class="form-control @error('languages_spoken') is-invalid @enderror" id="languages_spoken" name="languages_spoken" rows="3">{{ $listValue('languages_spoken') }}</textarea>
            @error('languages_spoken')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="skills">Key Skills</label>
            <textarea class="form-control @error('skills') is-invalid @enderror" id="skills" name="skills" rows="3">{{ $listValue('skills') }}</textarea>
            @error('skills')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="certifications">Certifications</label>
            <textarea class="form-control @error('certifications') is-invalid @enderror" id="certifications" name="certifications" rows="3">{{ $listValue('certifications') }}</textarea>
            @error('certifications')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="insurance_details">Insurance / Licensing Details</label>
        <textarea class="form-control @error('insurance_details') is-invalid @enderror" id="insurance_details" name="insurance_details" rows="3">{{ old('insurance_details', optional($expert)->insurance_details) }}</textarea>
        @error('insurance_details')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label for="response_time_hours">Avg. Response Time (hours)</label>
            <input type="number" min="1" class="form-control @error('response_time_hours') is-invalid @enderror" id="response_time_hours" name="response_time_hours" value="{{ old('response_time_hours', optional($expert)->response_time_hours) }}">
            @error('response_time_hours')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label for="completion_rate">Completion Rate (%)</label>
            <input type="number" step="0.1" min="0" max="100" class="form-control @error('completion_rate') is-invalid @enderror" id="completion_rate" name="completion_rate" value="{{ old('completion_rate', optional($expert)->completion_rate) }}">
            @error('completion_rate')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-4 d-flex align-items-center">
            <div class="form-check mt-4">
                <input class="form-check-input" type="checkbox" id="offers_emergency_service" name="offers_emergency_service" value="1" {{ old('offers_emergency_service', optional($expert)->offers_emergency_service) ? 'checked' : '' }}>
                <label class="form-check-label" for="offers_emergency_service">Offers emergency service</label>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Working Hours</strong>
        </div>
        <div class="card-body">
            <div class="form-row">
                @foreach($daysOfWeek as $dayKey => $dayLabel)
                    <div class="form-group col-md-4">
                        <label for="working_hours_{{ $dayKey }}">{{ $dayLabel }}</label>
                        <input type="text" class="form-control @error('working_hours.' . $dayKey) is-invalid @enderror" id="working_hours_{{ $dayKey }}" name="working_hours[{{ $dayKey }}]" placeholder="e.g. 9.00 AM - 6.00 PM" value="{{ old('working_hours.' . $dayKey, $workingHoursInput[$dayKey] ?? '') }}">
                        @error('working_hours.' . $dayKey)
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="profile_image">Profile Image</label>
        <input type="file" class="form-control-file @error('profile_image') is-invalid @enderror" id="profile_image" name="profile_image" accept="image/*">
        @if(optional($expert)->profile_image)
            <small class="form-text text-muted">Current: {{ optional($expert)->profile_image }}</small>
        @endif
        @error('profile_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label for="portfolio">Portfolio Images</label>
        <input type="file" class="form-control-file @error('portfolio.*') is-invalid @enderror" id="portfolio" name="portfolio[]" accept="image/*" multiple>
        @error('portfolio.*')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="text-right">
        <button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Expert' }}</button>
    </div>
</form>
