<div class="admin-sidebar">
    <ul class="admin-menu">
        <li class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="material-icons">dashboard</i>
                Dashboard
            </a>
        </li>
        
        <li class="menu-section">Content Management</li>
        
        <li class="{{ Request::routeIs('admin.content.*') ? 'active' : '' }}">
            <a href="{{ route('admin.content.index') }}">
                <i class="material-icons">assignment</i>
                All Content
            </a>
        </li>
        
        <li class="{{ Request::routeIs('admin.categories.*') ? 'active' : '' }}">
            <a href="{{ route('admin.categories.index') }}">
                <i class="material-icons">category</i>
                Categories
            </a>
        </li>
        
        <li class="{{ Request::routeIs('admin.locations.*') ? 'active' : '' }}">
            <a href="{{ route('admin.locations.index') }}">
                <i class="material-icons">location_on</i>
                Locations
            </a>
        </li>
        
        <li class="menu-section">User Management</li>
        
        <li class="{{ Request::routeIs('admin.users.*') ? 'active' : '' }}">
            <a href="{{ route('admin.users.index') }}">
                <i class="material-icons">people</i>
                Users
            </a>
        </li>
        
        <li class="{{ Request::routeIs('admin.reviews.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reviews.index') }}">
                <i class="material-icons">star</i>
                Reviews
            </a>
        </li>
        
        <li class="menu-section">Financial</li>
        
        <li class="{{ Request::routeIs('admin.financial.payments') ? 'active' : '' }}">
            <a href="{{ route('admin.financial.payments') }}">
                <i class="material-icons">payment</i>
                Payments
            </a>
        </li>
        
        <li class="{{ Request::routeIs('admin.financial.subscriptions') ? 'active' : '' }}">
            <a href="{{ route('admin.financial.subscriptions') }}">
                <i class="material-icons">card_membership</i>
                Subscriptions
            </a>
        </li>
        
        <li class="{{ Request::routeIs('admin.points.*') ? 'active' : '' }}">
            <a href="{{ route('admin.points.index') }}">
                <i class="material-icons">stars</i>
                Points
            </a>
        </li>
        
        <li class="{{ Request::routeIs('admin.financial.revenue') ? 'active' : '' }}">
            <a href="{{ route('admin.financial.revenue') }}">
                <i class="material-icons">trending_up</i>
                Revenue
            </a>
        </li>
        
        <li class="menu-section">Reports & Analytics</li>
        
        <li class="{{ Request::routeIs('admin.reports.*') ? 'active' : '' }}">
            <a href="{{ route('admin.reports.user-activity') }}">
                <i class="material-icons">assessment</i>
                Reports
            </a>
        </li>
        
        <li class="menu-section">Communication</li>
        
        <li class="{{ Request::routeIs('admin.notifications.*') ? 'active' : '' }}">
            <a href="{{ route('admin.notifications.index') }}">
                <i class="material-icons">notifications</i>
                Notifications
            </a>
        </li>
        
        <li class="menu-section">System</li>
        
        <li class="{{ Request::routeIs('admin.settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.index') }}">
                <i class="material-icons">settings</i>
                Settings
            </a>
        </li>
    </ul>
</div>
