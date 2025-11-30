<div class="dashboard-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="dash-head">
                    <a href="{{ route('home') }}" class="dash-logo">
                        <img src="{{ asset('images/home/16077bizbook-white.png') }}" alt="{{ config('app.name') }}">
                    </a>
                    
                    <div class="dash-user">
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="material-icons">account_circle</i>
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><a href="{{ route('points.index') }}">Points: <strong>{{ auth()->user()->points_balance }}</strong></a></li>
                                <li>
                                    <a href="{{ route('notifications.index') }}">
                                        Notifications
                                        @if(auth()->user()->notifications()->unread()->count() > 0)
                                            <span class="badge badge-danger">{{ auth()->user()->notifications()->unread()->count() }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li><hr></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Sign out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
