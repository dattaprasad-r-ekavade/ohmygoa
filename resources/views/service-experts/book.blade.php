@extends('layouts.public')

@section('title', 'Book ' . $expert->business_name)

@section('content')
<section class="pg-book">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card shadow-sm mb-4 mb-md-0">
                            <div class="card-body">
                                <h1 class="h4">{{ $expert->business_name }}</h1>
                                <p class="text-muted mb-2">{{ optional($expert->category)->name }} • {{ optional($expert->location)->name }}</p>
                                <p class="mb-2"><strong>Rating:</strong> {{ number_format($expert->average_rating, 1) }} ★ ({{ $expert->total_reviews }} reviews)</p>
                                <p class="mb-2"><strong>Experience:</strong> {{ $expert->years_of_experience }} yrs</p>
                                @if($expert->hourly_rate)
                                    <p class="mb-2"><strong>Hourly Rate:</strong> ₹{{ number_format($expert->hourly_rate, 2) }}</p>
                                @endif
                                @if($expert->service_areas)
                                    <p class="mb-0"><strong>Service Areas:</strong> {{ implode(', ', $expert->service_areas) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h2 class="h5 mb-0">Booking request</h2>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('service-experts.book.store', $expert) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="service_description">Describe the service you need</label>
                                        <textarea id="service_description" name="service_description" rows="4" class="form-control @error('service_description') is-invalid @enderror" required>{{ old('service_description') }}</textarea>
                                        @error('service_description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="preferred_date">Preferred date</label>
                                            <input type="date" id="preferred_date" name="preferred_date" class="form-control @error('preferred_date') is-invalid @enderror" value="{{ old('preferred_date') }}" required>
                                            @error('preferred_date')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="preferred_time">Preferred time</label>
                                            <input type="time" id="preferred_time" name="preferred_time" class="form-control @error('preferred_time') is-invalid @enderror" value="{{ old('preferred_time') }}">
                                            @error('preferred_time')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="location">Service location</label>
                                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $expert->address) }}" required>
                                        @error('location')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="contact_name">Your name</label>
                                            <input type="text" id="contact_name" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name', auth()->user()->name ?? '') }}" required>
                                            @error('contact_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="contact_phone">Phone number</label>
                                            <input type="text" id="contact_phone" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone', auth()->user()->phone ?? '') }}" required>
                                            @error('contact_phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_email">Email address</label>
                                        <input type="email" id="contact_email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" value="{{ old('contact_email', auth()->user()->email ?? '') }}">
                                        @error('contact_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="estimated_hours">Estimated hours (optional)</label>
                                        <input type="number" step="0.5" min="1" id="estimated_hours" name="estimated_hours" class="form-control @error('estimated_hours') is-invalid @enderror" value="{{ old('estimated_hours') }}">
                                        @error('estimated_hours')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        @if($expert->hourly_rate)
                                            <small class="form-text text-muted">Helper info: ₹{{ number_format($expert->hourly_rate, 2) }} x hours helps us create a quote.</small>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="special_instructions">Special instructions</label>
                                        <textarea id="special_instructions" name="special_instructions" rows="3" class="form-control @error('special_instructions') is-invalid @enderror">{{ old('special_instructions') }}</textarea>
                                        @error('special_instructions')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block">Submit booking request</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
