<section>
    <div class="str ind2-home">
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
                            <form name="filter_form" id="filter_form" class="filter_form" action="{{ route('search.results') }}" method="GET">
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
    </div>
</section>
