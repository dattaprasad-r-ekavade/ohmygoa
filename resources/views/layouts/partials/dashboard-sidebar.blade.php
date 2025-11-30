<div class="dashboard-sidebar">
    <ul class="dash-menu">
        <li class="{{ Request::routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}">
                <i class="material-icons">dashboard</i>
                Dashboard
            </a>
        </li>
        
        @if(auth()->user()->role === 'business' || auth()->user()->role === 'admin')
            <li class="{{ Request::routeIs('business.listings.*') ? 'active' : '' }}">
                <a href="{{ route('business.listings.index') }}">
                    <i class="material-icons">store</i>
                    My Listings
                </a>
            </li>
            
            <li class="{{ Request::routeIs('business.events.*') ? 'active' : '' }}">
                <a href="{{ route('business.events.index') }}">
                    <i class="material-icons">event</i>
                    My Events
                </a>
            </li>
            
            <li class="{{ Request::routeIs('business.jobs.*') ? 'active' : '' }}">
                <a href="{{ route('business.jobs.index') }}">
                    <i class="material-icons">work</i>
                    My Jobs
                </a>
            </li>
            
            <li class="{{ Request::routeIs('business.products.*') ? 'active' : '' }}">
                <a href="{{ route('business.products.index') }}">
                    <i class="material-icons">shopping_bag</i>
                    My Products
                </a>
            </li>
            
            <li class="{{ Request::routeIs('business.coupons.*') ? 'active' : '' }}">
                <a href="{{ route('business.coupons.index') }}">
                    <i class="material-icons">local_offer</i>
                    My Coupons
                </a>
            </li>
        @endif
        
        <li class="{{ Request::routeIs('classifieds.*') ? 'active' : '' }}">
            <a href="{{ route('classifieds.index') }}">
                <i class="material-icons">list_alt</i>
                My Classifieds
            </a>
        </li>
        
        <li class="{{ Request::routeIs('enquiries.*') ? 'active' : '' }}">
            <a href="{{ route('enquiries.my') }}">
                <i class="material-icons">question_answer</i>
                Enquiries
            </a>
        </li>
        
        <li class="{{ Request::routeIs('points.*') ? 'active' : '' }}">
            <a href="{{ route('points.index') }}">
                <i class="material-icons">stars</i>
                Points History
            </a>
        </li>
        
        <li class="{{ Request::routeIs('notifications.*') ? 'active' : '' }}">
            <a href="{{ route('notifications.index') }}">
                <i class="material-icons">notifications</i>
                Notifications
                @if(auth()->user()->notifications()->unread()->count() > 0)
                    <span class="badge badge-danger">{{ auth()->user()->notifications()->unread()->count() }}</span>
                @endif
            </a>
        </li>
        
        <li class="{{ Request::routeIs('media.*') ? 'active' : '' }}">
            <a href="{{ route('media.index') }}">
                <i class="material-icons">perm_media</i>
                Media Library
            </a>
        </li>
        
        <li class="{{ Request::routeIs('subscriptions.*') ? 'active' : '' }}">
            <a href="{{ route('subscriptions.index') }}">
                <i class="material-icons">card_membership</i>
                Subscription
                @if(auth()->user()->subscription)
                    <span class="badge badge-success">{{ auth()->user()->subscription->plan->name }}</span>
                @endif
            </a>
        </li>
        
        <li class="{{ Request::routeIs('profile.*') ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}">
                <i class="material-icons">settings</i>
                Settings
            </a>
        </li>
        
        @if(auth()->user()->role === 'admin')
            <li class="menu-divider"></li>
            <li class="{{ Request::routeIs('admin.*') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="material-icons">admin_panel_settings</i>
                    Admin Panel
                </a>
            </li>
        @endif
    </ul>
</div>
