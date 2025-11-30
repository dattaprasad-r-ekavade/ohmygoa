@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-page">
    <div class="dash-hea">
        <h1>Dashboard</h1>
        <p>Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-md-3">
            <div class="dash-stat-box">
                <i class="material-icons">stars</i>
                <h3>{{ number_format(auth()->user()->points_balance) }}</h3>
                <p>Points Balance</p>
                <a href="{{ route('points.index') }}">View Details</a>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dash-stat-box">
                <i class="material-icons">notifications</i>
                <h3>{{ auth()->user()->notifications()->unread()->count() }}</h3>
                <p>Unread Notifications</p>
                <a href="{{ route('notifications.index') }}">View All</a>
            </div>
        </div>
        
        @if(auth()->user()->role === 'business' || auth()->user()->role === 'admin')
            <div class="col-md-3">
                <div class="dash-stat-box">
                    <i class="material-icons">store</i>
                    <h3>{{ auth()->user()->listings()->count() }}</h3>
                    <p>My Listings</p>
                    <a href="{{ route('business.listings.index') }}">Manage</a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="dash-stat-box">
                    <i class="material-icons">event</i>
                    <h3>{{ auth()->user()->events()->count() }}</h3>
                    <p>My Events</p>
                    <a href="{{ route('business.events.index') }}">Manage</a>
                </div>
            </div>
        @else
            <div class="col-md-3">
                <div class="dash-stat-box">
                    <i class="material-icons">favorite</i>
                    <h3>{{ auth()->user()->bookmarks()->count() }}</h3>
                    <p>Bookmarks</p>
                    <a href="#">View All</a>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="dash-stat-box">
                    <i class="material-icons">work</i>
                    <h3>{{ auth()->user()->jobApplications()->count() }}</h3>
                    <p>Job Applications</p>
                    <a href="#">View All</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Activity -->
    <div class="dash-section">
        <h2>Recent Activity</h2>
        <div class="activity-list">
            @forelse(auth()->user()->notifications()->recent()->take(10)->get() as $notification)
                <div class="activity-item {{ $notification->is_read ? 'read' : 'unread' }}">
                    <div class="activity-icon">
                        <i class="material-icons">{{ $notification->icon ?? 'notifications' }}</i>
                    </div>
                    <div class="activity-content">
                        <h4>{{ $notification->title }}</h4>
                        <p>{{ $notification->message }}</p>
                        <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    @if($notification->action_url)
                        <a href="{{ $notification->action_url }}" class="activity-action">
                            {{ $notification->action_text ?? 'View' }}
                        </a>
                    @endif
                </div>
            @empty
                <p class="no-activity">No recent activity</p>
            @endforelse
        </div>
    </div>

    @if(auth()->user()->role === 'business' || auth()->user()->role === 'admin')
        <!-- Quick Actions -->
        <div class="dash-section">
            <h2>Quick Actions</h2>
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('business.listings.create') }}" class="quick-action-btn">
                        <i class="material-icons">add_business</i>
                        <span>Add Listing</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('business.events.create') }}" class="quick-action-btn">
                        <i class="material-icons">event</i>
                        <span>Create Event</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('business.jobs.create') }}" class="quick-action-btn">
                        <i class="material-icons">work</i>
                        <span>Post Job</span>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('business.products.create') }}" class="quick-action-btn">
                        <i class="material-icons">shopping_bag</i>
                        <span>Add Product</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Subscription Status -->
    @if(auth()->user()->subscription)
        <div class="dash-section">
            <h2>Subscription Status</h2>
            <div class="subscription-card">
                <div class="sub-info">
                    <h3>{{ auth()->user()->subscription->plan->name }} Plan</h3>
                    <p>Status: <span class="badge badge-{{ auth()->user()->subscription->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst(auth()->user()->subscription->status) }}</span></p>
                    <p>Expires: {{ auth()->user()->subscription->end_date->format('M d, Y') }}</p>
                </div>
                <div class="sub-actions">
                    @if(auth()->user()->subscription->status === 'active')
                        <a href="{{ route('subscriptions.upgrade') }}" class="btn btn-primary">Upgrade</a>
                        <a href="{{ route('subscriptions.cancel') }}" class="btn btn-secondary">Cancel</a>
                    @else
                        <a href="{{ route('subscriptions.renew', auth()->user()->subscription) }}" class="btn btn-success">Renew</a>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="dash-section">
            <div class="upgrade-prompt">
                <h3>Upgrade to Business Plan</h3>
                <p>Unlock unlimited listings, featured promotions, and more!</p>
                <a href="{{ route('subscriptions.index') }}" class="btn btn-success">View Plans</a>
            </div>
        </div>
    @endif
</div>
@endsection
