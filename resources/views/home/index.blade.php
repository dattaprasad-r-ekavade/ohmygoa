@extends('layouts.public')

@section('title', 'Home')

@section('header')
@endsection

@section('content')
<section>
    <div class="str ind2-home">
        <div class="hom-head" style="background-image: url({{ asset('images/3261288129ex2.jpg') }});">
            <div class="hom-top">
                <div class="container">
                    <div class="row">
                        <div class="hom-nav">
                            <!--MOBILE MENU-->
                            <a href="{{ route('home') }}" class="top-log">
                                <img src="{{ asset('images/home/16077bizbook-white.png') }}" alt="{{ config('app.name') }}" loading="eager" class="ic-logo">
                            </a>
                            <div class="menu">
                                <h4>Explore</h4>
                            </div>
                            <div class="pop-menu"></div>
                            <!--END MOBILE MENU-->
                            
                            <div class="top-ser">
                                <form name="filter_form" id="filter_form" class="filter_form" action="{{ route('search.index') }}" method="GET">
                                    <ul>
                                        <li class="sr-sea">
                                            <input type="text" name="q" autocomplete="off" id="top-select-search" placeholder="What are you looking for?" value="{{ request('q') }}">
                                            <ul id="tser-res1" class="tser-res tser-res2"></ul>
                                        </li>
                                        <li class="sbtn">
                                            <button type="submit" class="btn btn-success">
                                                <i class="material-icons">search</i>
                                            </button>
                                        </li>
                                    </ul>
                                </form>
                            </div>
                            
                            <ul class="bl">
                                @auth
                                    @if(auth()->user()->role === 'admin')
                                        <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                                    @elseif(auth()->user()->role === 'business')
                                        <li><a href="{{ route('business.dashboard') }}">Dashboard</a></li>
                                    @else
                                        <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                    @endif
                                    
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            {{ auth()->user()->name }}
                                            <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                            <li><a href="{{ route('notifications.index') }}">
                                                Notifications 
                                                @if(auth()->user()->notifications()->unread()->count() > 0)
                                                    <span class="badge">{{ auth()->user()->notifications()->unread()->count() }}</span>
                                                @endif
                                            </a></li>
                                            <li><a href="{{ route('points.index') }}">Points: {{ auth()->user()->points_balance }}</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item">Sign out</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                @else
                                    <li><a href="{{ route('subscriptions.index') }}">Add business</a></li>
                                    <li><a href="{{ route('login') }}">Sign in</a></li>
                                    <li><a href="{{ route('register') }}">Create an account</a></li>
                                @endauth
                            </ul>
                            
                            <!--MOBILE MENU-->
                            <div class="mob-menu">
                                <div class="mob-me-ic">
                                    <i class="material-icons">menu</i>
                                </div>
                                <div class="mob-me-all">
                                    <div class="mob-me-clo">
                                        <i class="material-icons">close</i>
                                    </div>
                                    <div class="mv-bus">
                                        <h4></h4>
                                        <ul>
                                            @auth
                                                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                                <li><a href="{{ route('notifications.index') }}">Notifications</a></li>
                                                <li><a href="{{ route('points.index') }}">Points</a></li>
                                                <li>
                                                    <form action="{{ route('logout') }}" method="POST">
                                                        @csrf
                                                        <button type="submit">Sign out</button>
                                                    </form>
                                                </li>
                                            @else
                                                <li><a href="{{ route('subscriptions.index') }}">Add business</a></li>
                                                <li><a href="{{ route('login') }}">Sign in</a></li>
                                                <li><a href="{{ route('register') }}">Create an account</a></li>
                                            @endauth
                                        </ul>
                                    </div>
                                    @include('layouts.partials.mobile-menu')
                                </div>
                            </div>
                            <!--END MOBILE MENU-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="ban-tit">
                        <h1>
                            <b>Find your
                                <span>Local needs
                                    <i></i>
                                </span>
                            </b>
                            Restaurants, cafe's, and bars in New york
                        </h1>
                    </div>
                    <div class="ban-search ban-sear-all">
                        <form action="{{ route('search.index') }}" method="GET" name="filter_form" id="filter_form" class="filter_form">
                            <ul>
                                <li class="sr-cate">
                                    <select name="category_id" class="chosen-select">
                                        <option value="">All Services</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </li>
                                <li class="sr-cit">
                                    <select name="location_id" class="chosen-select">
                                        <option value="">All Locations</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                </li>
                                <li class="sr-nor">
                                    <input type="text" name="q" autocomplete="off" id="top-select-search" placeholder="What are you looking for?">
                                </li>
                                <li class="sr-btn">
                                    <button type="submit" class="btn btn-success" style="width: 100%; height: 58px; border-radius: 0 10px 10px 0;">
                                        <i class="material-icons">search</i>
                                    </button>
                                </li>
                            </ul>
                        </form>
                    </div>
                    <div class="ban-short-links">
                        <ul>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/shop.png') }}" alt="" loading="lazy">
                                    <h4>All Services</h4>
                                    <a href="{{ route('search.index') }}" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/ads.png') }}" alt="" loading="lazy">
                                    <h4>Classifieds</h4>
                                    <a href="#" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/expert.png') }}" alt="" loading="lazy">
                                    <h4>Experts</h4>
                                    <a href="#" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/employee.png') }}" alt="" loading="lazy">
                                    <h4>Jobs</h4>
                                    <a href="{{ route('jobs.index') }}" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/places/icons/hot-air-balloon.png') }}" alt="" loading="lazy">
                                    <h4>Travel</h4>
                                    <a href="#" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/news.png') }}" alt="" loading="lazy">
                                    <h4>News</h4>
                                    <a href="#" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/calendar.png') }}" alt="" loading="lazy">
                                    <h4>Events</h4>
                                    <a href="{{ route('events.index') }}" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/cart.png') }}" alt="" loading="lazy">
                                    <h4>Products</h4>
                                    <a href="{{ route('products.index') }}" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/coupons.png') }}" alt="" loading="lazy">
                                    <h4>Coupons</h4>
                                    <a href="#" class="fclick"></a>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img src="{{ asset('images/icon/blog1.png') }}" alt="" loading="lazy">
                                    <h4>Blogs</h4>
                                    <a href="#" class="fclick"></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="ban-ql">
    <div class="container">
        <div class="row">
            <ul>
                <li>
                    <div>
                        <img src="{{ asset('images/icon/1.png') }}" alt="" loading="lazy">
                        <h4>{{ number_format($stats['total_listings']) }} Business</h4>
                        <p>Choose from a collection of handpicked luxury villas & apartments</p>
                        <a href="{{ route('search.index') }}">Explore Now</a>
                    </div>
                </li>
                <li>
                    <div>
                        <img src="{{ asset('images/icon/2.png') }}" alt="" loading="lazy">
                        <h4>{{ number_format($stats['total_users']) }}+ Service Experts</h4>
                        <p>Are you looking for the best Service Expert? We make it easy to hire the right professional</p>
                        <a href="#">Book Expert Now</a>
                    </div>
                </li>
                <li>
                    <div>
                        <img src="{{ asset('images/icon/3.png') }}" alt="" loading="lazy">
                        <h4>Find Your Next Job Now</h4>
                        <p>Search latest job openings online including IT, Sales, Banking, Fresher, Walk-ins, Part-time & more</p>
                        <a href="{{ route('jobs.index') }}">Find you Job</a>
                    </div>
                </li>
                <li>
                    <div>
                        <img src="{{ asset('images/icon/4.png') }}" alt="" loading="lazy">
                        <h4>Sell & Buy Product Online</h4>
                        <p>Bizbook Online store. Everything you need to sell & buy online.</p>
                        <a href="{{ route('products.index') }}">Start Selling Online</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>


<section class="wed-hom-body">
    <div class="container">
        <div class="row">
            <div class="home-tit">
                <h2>
                    <span>Top Services</span> Categories
                </h2>
                <p>Explore some of the best business from around the world from our partners and friends.</p>
            </div>
            <div class="land-pack">
                <ul>
                    @foreach($categories as $category)
                        <li>
                            <div class="land-pack-grid">
                                <div class="land-pack-grid-img">
                                    <img src="{{ $category->image ? asset('storage/'.$category->image) : asset('images/services/1.jpg') }}" alt="{{ $category->name }}" loading="lazy">
                                </div>
                                <div class="land-pack-grid-text">
                                    <h4>{{ $category->name }}</h4>
                                    <a href="{{ route('search.index', ['category_id' => $category->id]) }}" class="land-pack-grid-btn">View all listings</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="wed-hom-body-1">
    <div class="container">
        <div class="row">
            <div class="home-tit">
                <h2>
                    <span>Explore</span> City
                </h2>
                <p>Explore some of the best business from around the world from our partners and friends.</p>
            </div>
            <div class="land-pack">
                <ul>
                    @foreach($locations as $location)
                        <li>
                            <div class="land-pack-grid">
                                <div class="land-pack-grid-img">
                                    <img src="{{ asset('images/services/9.jpg') }}" alt="{{ $location->name }}" loading="lazy">
                                </div>
                                <div class="land-pack-grid-text">
                                    <h4>{{ $location->name }}</h4>
                                    <a href="{{ route('search.index', ['location_id' => $location->id]) }}" class="land-pack-grid-btn">View all listings</a>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

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
