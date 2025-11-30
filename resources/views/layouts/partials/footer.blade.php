<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="fot-col">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/home/16077bizbook-white.png') }}" alt="{{ config('app.name') }}">
                    </a>
                    <p>{{ config('app.name') }} - Your complete directory for Goa businesses, events, jobs, and more.</p>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="fot-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="{{ route('listings.index') }}">Business Listings</a></li>
                        <li><a href="{{ route('events.index') }}">Events</a></li>
                        <li><a href="{{ route('jobs.index') }}">Jobs</a></li>
                        <li><a href="{{ route('products.index') }}">Products</a></li>
                        <li><a href="{{ route('coupons.index') }}">Coupons</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="fot-col">
                    <h4>More</h4>
                    <ul>
                        <li><a href="{{ route('service-experts.index') }}">Service Experts</a></li>
                        <li><a href="{{ route('classifieds.index') }}">Classifieds</a></li>
                        <li><a href="{{ route('news.index') }}">News</a></li>
                        <li><a href="{{ route('places.index') }}">Places to Visit</a></li>
                        <li><a href="{{ route('qa.index') }}">Community</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="fot-col">
                    <h4>Information</h4>
                    <ul>
                        <li><a href="{{ route('about') }}">About Us</a></li>
                        <li><a href="{{ route('contact') }}">Contact Us</a></li>
                        <li><a href="{{ route('how-to') }}">How It Works</a></li>
                        <li><a href="{{ route('faq') }}">FAQ</a></li>
                        <li><a href="{{ route('terms') }}">Terms of Use</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="fot-bot">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        {{ session('info') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
