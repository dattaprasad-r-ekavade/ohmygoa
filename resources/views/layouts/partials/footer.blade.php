<section>
    <div class="full-bot-book">
        <div class="container">
            <div class="row">
                <div class="bot-book">
                    <div class="col-md-12 bb-text">
                        <h4>Ready to grow your business?</h4>
                        <p>Join thousands of businesses already listed on {{ config('app.name') }}</p>
                        <a href="{{ route('subscriptions.index') }}">Add my business
                            <i class="material-icons">arrow_forward</i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="wed-hom-footer">
    <div class="container">
        <div class="row foot-supp">
            <h2>
                <span>Free support:</span> +01 5426 24400 &nbsp;&nbsp;|&nbsp;&nbsp;
                <span>Email:</span> rn53themes@gmail.com
            </h2>
        </div>
        <div class="row wed-foot-link">
            <div class="col-md-4 foot-tc-mar-t-o">
                <h4>Top Category</h4>
                <ul>
                    <li><a href="{{ route('search.index', ['category' => 'technology']) }}">Technology</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'spa']) }}">Spa and Facial</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'real-estate']) }}">Real Estate</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'sports']) }}">Sports</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'education']) }}">Education</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'electricals']) }}">Electricals</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'automobiles']) }}">Automobiles</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'transportation']) }}">Transportation</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h4>Trending Category</h4>
                <ul>
                    <li><a href="{{ route('search.index', ['category' => 'hospitals']) }}">Hospitals</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'automobiles']) }}">Automobiles</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'real-estate']) }}">Real Estate</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'sports']) }}">Sports</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'education']) }}">Education</a></li>
                    <li><a href="{{ route('search.index', ['category' => 'electricals']) }}">Electricals</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h4>HELP & SUPPORT</h4>
                <ul>
                    <li><a href="{{ route('about') }}">About us</a></li>
                    <li><a href="{{ route('faq') }}">FAQ</a></li>
                    <li><a href="{{ route('feedback') }}">Feedback</a></li>
                    <li><a href="{{ route('contact') }}">Contact us</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}">Terms of Use</a></li>
                </ul>
            </div>
        </div>
        
        <!-- POPULAR TAGS -->
        <div class="row wed-foot-link-pop">
            <div class="col-md-12">
                <h4>Popular Tags</h4>
                <ul>
                    <li><a href="{{ route('search.index', ['q' => 'Schools in NewYork']) }}">Schools in NewYork</a></li>
                    <li><a href="{{ route('search.index', ['q' => 'Real estate in Illunois']) }}">Real estate in Illunois</a></li>
                    <li><a href="{{ route('search.index', ['q' => 'Real estate in Chennai1']) }}">Real estate in Chennai1</a></li>
                    <li><a href="{{ route('search.index', ['q' => 'Enents in Tailand']) }}">Enents in Tailand</a></li>
                    <li><a href="{{ route('search.index', ['q' => 'Flat for rent in Melborn']) }}">Flat for rent in Melborn</a></li>
                </ul>
            </div>
        </div>
        
        <div class="row wed-foot-link-1">
            <div class="col-md-4">
                <h4>Get In Touch</h4>
                <p>Address: 28800 Orchard Lake Road, Suite 180 Farmington Hills, U.S.A.</p>
                <p>Phone: <a href="tel:+01 5426 24400">+01 5426 24400</a></p>
                <p>Email: <a href="mailto:rn53themes@gmail.com">rn53themes@gmail.com</a></p>
            </div>
            <div class="col-md-4 fot-app">
                <h4>DOWNLOAD OUR FREE MOBILE APPS</h4>
                <ul>
                    <li><a href=""><img src="{{ asset('images/gstore.png') }}" alt="" loading="lazy"></a></li>
                    <li><a href=""><img src="{{ asset('images/astore.png') }}" alt="" loading="lazy"></a></li>
                </ul>
            </div>
            <div class="col-md-4 fot-soc">
                <h4>SOCIAL MEDIA</h4>
                <ul>
                    <li><a target="_blank" href=""><img src="{{ asset('images/social/1.png') }}" alt="" loading="lazy"></a></li>
                    <li><a target="_blank" href=""><img src="{{ asset('images/social/2.png') }}" alt="" loading="lazy"></a></li>
                    <li><a target="_blank" href=""><img src="{{ asset('images/social/3.png') }}" alt="" loading="lazy"></a></li>
                    <li><a target="_blank" href=""><img src="{{ asset('images/social/4.png') }}" alt="" loading="lazy"></a></li>
                    <li><a target="_blank" href=""><img src="{{ asset('images/social/5.png') }}" alt="" loading="lazy"></a></li>
                </ul>
            </div>
        </div>
        
        <div class="row foot-count">
            <ul>
                <li><a target="_blank" href="#">Australia</a></li>
                <li><a target="_blank" href="#">UK</a></li>
                <li><a target="_blank" href="#">USA</a></li>
                <li><a target="_blank" href="#">India</a></li>
                <li><a target="_blank" href="#">Germany</a></li>
            </ul>
        </div>
    </div>
</section>

<section class="wed-rights">
    <div class="container">
        <div class="row">
            <div class="copy-right">
               <a target="_blank" href="https://www.templatespoint.net">Templates Point</a>
            </div>
        </div>
    </div>
</section>

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
