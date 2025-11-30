<div class="admin-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="admin-head">
                    <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                        <img src="{{ asset('images/home/16077bizbook-white.png') }}" alt="{{ config('app.name') }}">
                        <span class="admin-badge">ADMIN</span>
                    </a>
                    
                    <div class="admin-actions">
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light" target="_blank">
                            <i class="material-icons">open_in_new</i>
                            View Site
                        </a>
                        
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="material-icons">account_circle</i>
                                {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><a href="{{ route('dashboard') }}">User Dashboard</a></li>
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
