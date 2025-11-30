@extends('layouts.public')

@section('title', 'Home')

@section('content')
<section>
    <div class="str ind2-home">
        <!-- Hero Section -->
        <div class="hom-head" style="background-image: url({{ asset('images/3261288129ex2.jpg') }});">
            <div class="container">
                <div class="row">
                    <div class="hom-ban">
                        <div class="ban-tit">
                            <h2><span>Discover</span> The Best Of <span>Goa</span></h2>
                            <p>Explore businesses, events, jobs & more in beautiful Goa!</p>
                        </div>
                        
                        <div class="hom-ser">
                            <form action="{{ route('search.results') }}" method="GET" class="hom-ser-form">
                                <div class="row">
                                    <div class="col-md-5">
                                        <input type="text" name="q" class="form-control" placeholder="What are you looking for?" required>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="category" class="form-control">
                                            <option value="">All Categories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="location" class="form-control">
                                            <option value="">All Locations</option>
                                            @foreach($locations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-icons">search</i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="container">
            <div class="row">
                <div class="home-stats">
                    <div class="col-md-3">
                        <div class="stat-box">
                            <i class="material-icons">store</i>
                            <h3>{{ number_format($stats['total_listings']) }}</h3>
                            <p>Business Listings</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <i class="material-icons">people</i>
                            <h3>{{ number_format($stats['total_users']) }}</h3>
                            <p>Active Users</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <i class="material-icons">event</i>
                            <h3>{{ number_format($stats['total_events']) }}</h3>
                            <p>Events</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <i class="material-icons">work</i>
                            <h3>{{ number_format($stats['total_jobs']) }}</h3>
                            <p>Job Openings</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Categories -->
        <div class="container">
            <div class="row">
                <div class="home-tit">
                    <h2>Browse by Category</h2>
                    <p>Explore popular categories</p>
                </div>
            </div>
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('listings.index', ['category' => $category->id]) }}" class="category-box">
                            <i class="material-icons">{{ $category->icon ?? 'category' }}</i>
                            <h4>{{ $category->name }}</h4>
                            <p>{{ $category->listings_count ?? 0 }} listings</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Featured Listings -->
        @if($featuredListings->count() > 0)
        <div class="container">
            <div class="row">
                <div class="home-tit">
                    <h2>Featured Businesses</h2>
                    <p>Top rated businesses in Goa</p>
                    <a href="{{ route('listings.index') }}" class="btn btn-primary">View All</a>
                </div>
            </div>
            <div class="row">
                @foreach($featuredListings as $listing)
                    <div class="col-md-3 col-sm-6">
                        @include('partials.listing-card', ['listing' => $listing])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Upcoming Events -->
        @if($upcomingEvents->count() > 0)
        <div class="container">
            <div class="row">
                <div class="home-tit">
                    <h2>Upcoming Events</h2>
                    <p>Don't miss these exciting events</p>
                    <a href="{{ route('events.index') }}" class="btn btn-primary">View All</a>
                </div>
            </div>
            <div class="row">
                @foreach($upcomingEvents as $event)
                    <div class="col-md-4 col-sm-6">
                        @include('partials.event-card', ['event' => $event])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Latest Jobs -->
        @if($latestJobs->count() > 0)
        <div class="container">
            <div class="row">
                <div class="home-tit">
                    <h2>Latest Jobs</h2>
                    <p>Find your dream job in Goa</p>
                    <a href="{{ route('jobs.index') }}" class="btn btn-primary">View All</a>
                </div>
            </div>
            <div class="row">
                @foreach($latestJobs as $job)
                    <div class="col-md-4 col-sm-6">
                        @include('partials.job-card', ['job' => $job])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Featured Products -->
        @if($featuredProducts->count() > 0)
        <div class="container">
            <div class="row">
                <div class="home-tit">
                    <h2>Featured Products</h2>
                    <p>Shop from local businesses</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">View All</a>
                </div>
            </div>
            <div class="row">
                @foreach($featuredProducts as $product)
                    <div class="col-md-3 col-sm-6">
                        @include('partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Call to Action -->
        <div class="container">
            <div class="row">
                <div class="home-cta">
                    <h2>Ready to grow your business?</h2>
                    <p>Join thousands of businesses already listed on {{ config('app.name') }}</p>
                    <a href="{{ route('subscriptions.index') }}" class="btn btn-lg btn-success">Get Started</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Auto-complete for search
    $('#top-select-search').autocomplete({
        source: '{{ route('search.autocomplete') }}',
        minLength: 2,
        select: function(event, ui) {
            window.location.href = ui.item.url;
        }
    });
</script>
@endpush
