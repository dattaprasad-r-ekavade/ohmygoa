@extends('layouts.public')

@section('title', $expert->business_name)

@section('content')
<section class="pg-detail">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <div class="media align-items-center">
                    <div class="mr-4">
                        <img src="{{ $expert->profile_image ? asset('storage/' . $expert->profile_image) : asset('images/services/1.jpg') }}" alt="{{ $expert->business_name }}" class="rounded-circle" width="120" height="120">
                    </div>
                    <div class="media-body">
                        <h1 class="mb-1">{{ $expert->business_name }}</h1>
                        <p class="mb-2 text-muted">
                            {{ optional($expert->category)->name }} • {{ optional($expert->location)->name }}
                        </p>
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="badge badge-success mr-3">{{ number_format($expert->average_rating, 1) }} / 5 • {{ $expert->total_reviews }} reviews</span>
                            <span class="mr-3">{{ $expert->years_of_experience }} yrs experience</span>
                            <span>{{ $expert->jobs_completed }} jobs completed</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <p class="h5 mb-2">
                    @if($expert->hourly_rate)
                        ₹{{ number_format($expert->hourly_rate, 2) }} / hour
                    @else
                        <span class="text-muted">Contact for quote</span>
                    @endif
                </p>
                <div class="d-flex justify-content-md-end flex-wrap">
                    <span class="badge badge-pill badge-info mr-2 mb-2">{{ $expert->is_available ? 'Available' : 'Currently booked' }}</span>
                    @if($expert->offers_emergency_service)
                        <span class="badge badge-pill badge-warning mb-2">Emergency 24/7</span>
                    @endif
                </div>
                @auth
                    @if($expert->is_available)
                        <a href="{{ route('service-experts.book', $expert) }}" class="btn btn-primary btn-block mt-2">Book this expert</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-block mt-2">Sign in to book</a>
                @endauth
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h2 class="h5 mb-0">About {{ $expert->business_name }}</h2>
                    </div>
                    <div class="card-body">
                        <p>{{ $expert->description }}</p>
                        @if($expert->services_offered)
                            <h3 class="h6 mt-4">Services offered</h3>
                            <ul class="list-unstyled row">
                                @foreach($expert->services_offered as $service)
                                    <li class="col-md-6 mb-2">
                                        <i class="material-icons text-primary mr-1" style="font-size:18px;vertical-align:middle;">check_circle</i> {{ $service }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($expert->service_areas)
                            <h3 class="h6 mt-3">Service areas</h3>
                            <p class="mb-0">{{ implode(', ', $expert->service_areas) }}</p>
                        @endif
                    </div>
                </div>

                @if($reviews->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h2 class="h5 mb-0">Recent reviews</h2>
                            <span class="text-muted small">{{ $expert->total_reviews }} total</span>
                        </div>
                        <div class="card-body">
                            @foreach($reviews as $review)
                                <div class="media mb-4 pb-4 border-bottom">
                                    <div class="media-body">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong>{{ optional($review->user)->name ?? 'Anonymous User' }}</strong>
                                            <span class="text-warning">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                                        </div>
                                        <p class="mb-1">{{ $review->comment }}</p>
                                        <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                            {{ $reviews->links() }}
                        </div>
                    </div>
                @else
                    <div class="alert alert-light">No reviews yet. Be the first to book and share your experience.</div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h2 class="h6 mb-0">Contact & availability</h2>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Phone:</strong> {{ $expert->contact_phone }}</p>
                        @if($expert->contact_email)
                            <p class="mb-2"><strong>Email:</strong> {{ $expert->contact_email }}</p>
                        @endif
                        @if($expert->website)
                            <p class="mb-2"><strong>Website:</strong> <a href="{{ $expert->website }}" target="_blank" rel="noopener">Visit site</a></p>
                        @endif
                        @if($expert->address)
                            <p class="mb-2"><strong>Address:</strong> {{ $expert->address }}</p>
                        @endif
                        @if($expert->availability)
                            <p class="mb-2"><strong>Availability:</strong> {{ $expert->availability }}</p>
                        @endif
                        @if($expert->working_hours)
                            <h3 class="h6 mt-3">Working hours</h3>
                            <ul class="list-unstyled mb-0">
                                @foreach($expert->working_hours as $day => $hours)
                                    <li class="d-flex justify-content-between">
                                        <span class="text-capitalize">{{ $day }}</span>
                                        <span>{{ $hours }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                @if($expert->skills)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h2 class="h6 mb-0">Skills & certifications</h2>
                        </div>
                        <div class="card-body">
                            <p class="mb-2"><strong>Skills:</strong> {{ implode(', ', $expert->skills) }}</p>
                            @if($expert->certifications)
                                <p class="mb-0"><strong>Certifications:</strong> {{ implode(', ', $expert->certifications) }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                @if($expert->languages_spoken)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h2 class="h6 mb-0">Languages</h2>
                        </div>
                        <div class="card-body">
                            {{ implode(', ', $expert->languages_spoken) }}
                        </div>
                    </div>
                @endif

                @if($similarExperts->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h2 class="h6 mb-0">Similar experts</h2>
                        </div>
                        <div class="card-body">
                            @foreach($similarExperts as $similar)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('service-experts.show', ['slug' => $similar->slug]) }}">{{ $similar->business_name }}</a>
                                        <span class="text-muted">{{ number_format($similar->average_rating, 1) }} ★</span>
                                    </div>
                                    <small class="text-muted">{{ optional($similar->location)->name }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
