@extends('layouts.public')

@section('title', 'Trusted Service Experts in Goa')

@section('content')
<section class="pg-list">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="pg-tit">
                    <h1>Find verified professionals for every local need</h1>
                    <p>Compare ratings, pricing, availability, and book directly with Goa's most trusted electricians, photographers, event planners, home service providers, and more.</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <form method="GET" action="{{ route('service-experts.index') }}" class="card shadow-sm p-3">
                    <div class="form-row align-items-end">
                        <div class="form-group col-md-3">
                            <label for="search">Keyword</label>
                            <input type="text" id="search" name="search" class="form-control" placeholder="Plumber, Photographer..." value="{{ request('search') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="category">Category</label>
                            <select id="category" name="category" class="form-control">
                                <option value="">All categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) request('category') === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="location">Location</label>
                            <select id="location" name="location" class="form-control">
                                <option value="">All locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ (string) request('location') === (string) $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="experience">Min. experience (yrs)</label>
                            <input type="number" min="0" id="experience" name="experience" class="form-control" value="{{ request('experience') }}">
                        </div>
                    </div>

                    <div class="form-row align-items-center">
                        <div class="form-group col-md-2">
                            <label for="min_price">Min rate (₹)</label>
                            <input type="number" min="0" id="min_price" name="min_price" class="form-control" value="{{ request('min_price') }}">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="max_price">Max rate (₹)</label>
                            <input type="number" min="0" id="max_price" name="max_price" class="form-control" value="{{ request('max_price') }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="sort">Sort by</label>
                            <select id="sort" name="sort" class="form-control">
                                <option value="created_at" {{ request('sort', 'created_at') === 'created_at' ? 'selected' : '' }}>Newest</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Highest rated</option>
                                <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Price</option>
                                <option value="experience" {{ request('sort') === 'experience' ? 'selected' : '' }}>Experience</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most viewed</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="order">Order</label>
                            <select id="order" name="order" class="form-control">
                                <option value="desc" {{ request('order', 'desc') === 'desc' ? 'selected' : '' }}>Descending</option>
                                <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <div class="form-check mt-4">
                                <input type="checkbox" class="form-check-input" id="available" name="available" value="1" {{ request()->filled('available') ? 'checked' : '' }}>
                                <label class="form-check-label" for="available">Available now</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ request()->filled('featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">Featured only</label>
                            </div>
                        </div>
                        <div class="form-group col-md-1 text-right">
                            <button type="submit" class="btn btn-primary btn-block">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            @forelse($experts as $expert)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card service-card h-100">
                        <div class="card-img-top service-card-img" style="background-image:url('{{ $expert->profile_image ? asset('storage/' . $expert->profile_image) : asset('images/services/1.jpg') }}');"></div>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h3 class="h5 mb-1">
                                        <a href="{{ route('service-experts.show', ['slug' => $expert->slug]) }}">{{ $expert->business_name }}</a>
                                    </h3>
                                    <p class="text-muted mb-0">{{ optional($expert->category)->name }} • {{ optional($expert->location)->name }}</p>
                                </div>
                                <span class="badge badge-pill badge-success">{{ number_format($expert->average_rating, 1) }} ★</span>
                            </div>
                            <p class="text-muted small mb-2">{{ $expert->years_of_experience }} yrs experience • {{ $expert->response_time_hours ? $expert->response_time_hours . 'h response' : 'Fast response' }}</p>
                            @if($expert->services_offered)
                                <p class="mb-3 small">
                                    <strong>Services:</strong>
                                    {{ collect($expert->services_offered)->take(3)->implode(', ') }}@if(count($expert->services_offered) > 3) ... @endif
                                </p>
                            @endif
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div>
                                    @if($expert->hourly_rate)
                                        <span class="text-primary font-weight-semibold">₹{{ number_format($expert->hourly_rate, 2) }}/hr</span>
                                    @else
                                        <span class="text-muted">Contact for quote</span>
                                    @endif
                                </div>
                                <a href="{{ route('service-experts.show', ['slug' => $expert->slug]) }}" class="btn btn-outline-primary btn-sm">View profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No experts match your filters yet. Try broadening your search or check back soon as new professionals join daily.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="row">
            <div class="col-12">
                {{ $experts->withQueryString()->links() }}
            </div>
        </div>
    </div>
</section>
@endsection
