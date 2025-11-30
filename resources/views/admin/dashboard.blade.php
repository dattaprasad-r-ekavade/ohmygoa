@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <div class="dash-hea">
        <h1>Admin Dashboard</h1>
        <p>Overview of your platform</p>
    </div>

    <!-- Quick Stats -->
    <div class="row">
        <div class="col-md-3">
            <div class="dash-stat-box stat-users">
                <i class="material-icons">people</i>
                <h3>{{ number_format($totalUsers) }}</h3>
                <p>Total Users</p>
                <span class="stat-change">
                    <i class="material-icons">arrow_upward</i>
                    {{ $newUsersThisMonth }} this month
                </span>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dash-stat-box stat-listings">
                <i class="material-icons">store</i>
                <h3>{{ number_format($totalListings) }}</h3>
                <p>Total Listings</p>
                @if($pendingListings > 0)
                    <span class="stat-pending">{{ $pendingListings }} pending approval</span>
                @endif
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dash-stat-box stat-revenue">
                <i class="material-icons">attach_money</i>
                <h3>₹{{ number_format($totalRevenue) }}</h3>
                <p>Total Revenue</p>
                <span class="stat-change">
                    ₹{{ number_format($revenueThisMonth) }} this month
                </span>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="dash-stat-box stat-pending">
                <i class="material-icons">pending_actions</i>
                <h3>{{ $pendingApprovals['total'] }}</h3>
                <p>Pending Approvals</p>
                <a href="{{ route('admin.content.index', ['status' => 'pending']) }}">Review Now</a>
            </div>
        </div>
    </div>

    <!-- Content Overview -->
    <div class="row">
        <div class="col-md-8">
            <div class="admin-panel">
                <h3>Content Statistics</h3>
                <div class="content-stats-grid">
                    <div class="content-stat-item">
                        <h4>{{ number_format($totalListings) }}</h4>
                        <p>Listings</p>
                        <small>{{ $activeListings }} active</small>
                    </div>
                    <div class="content-stat-item">
                        <h4>{{ number_format($totalEvents) }}</h4>
                        <p>Events</p>
                        <small>{{ $upcomingEvents }} upcoming</small>
                    </div>
                    <div class="content-stat-item">
                        <h4>{{ number_format($totalJobs) }}</h4>
                        <p>Jobs</p>
                        <small>{{ $activeJobs }} active</small>
                    </div>
                    <div class="content-stat-item">
                        <h4>{{ number_format($totalProducts) }}</h4>
                        <p>Products</p>
                        <small>{{ $pendingProducts }} pending</small>
                    </div>
                    <div class="content-stat-item">
                        <h4>{{ number_format($totalClassifieds) }}</h4>
                        <p>Classifieds</p>
                        <small>{{ $pendingClassifieds }} pending</small>
                    </div>
                    <div class="content-stat-item">
                        <h4>{{ number_format($totalServiceExperts) }}</h4>
                        <p>Service Experts</p>
                        <small>{{ $pendingServiceExperts }} pending</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="admin-panel">
                <h3>User Distribution</h3>
                <div class="user-distribution">
                    <div class="dist-item">
                        <span class="label">Business Users</span>
                        <span class="value">{{ number_format($businessUsers) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($businessUsers / $totalUsers) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="dist-item">
                        <span class="label">Free Users</span>
                        <span class="value">{{ number_format($totalUsers - $businessUsers) }}</span>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ (($totalUsers - $businessUsers) / $totalUsers) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="dist-item">
                        <span class="label">Active Users</span>
                        <span class="value">{{ number_format($activeUsers) }}</span>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: {{ ($activeUsers / $totalUsers) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-6">
            <div class="admin-panel">
                <h3>User Registrations (Last 12 Months)</h3>
                <canvas id="userRegistrationsChart"></canvas>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="admin-panel">
                <h3>Monthly Revenue (Last 12 Months)</h3>
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="row">
        <div class="col-md-6">
            <div class="admin-panel">
                <h3>Top Categories</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Listings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCategories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ number_format($category->count) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="admin-panel">
                <h3>Top Locations</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Listings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topLocations as $location)
                            <tr>
                                <td>{{ $location->name }}</td>
                                <td>{{ number_format($location->count) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-4">
            <div class="admin-panel">
                <h3>Recent Listings</h3>
                <div class="recent-list">
                    @foreach($recentListings as $listing)
                        <div class="recent-item">
                            <h5>
                                <a href="{{ route('admin.content.listings.show', $listing) }}">
                                    {{ $listing->title }}
                                </a>
                            </h5>
                            <p>{{ $listing->user->name }}</p>
                            <small>{{ $listing->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="admin-panel">
                <h3>Recent Users</h3>
                <div class="recent-list">
                    @foreach($recentUsers as $user)
                        <div class="recent-item">
                            <h5>
                                <a href="{{ route('admin.users.show', $user) }}">
                                    {{ $user->name }}
                                </a>
                            </h5>
                            <p>{{ $user->email }}</p>
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="admin-panel">
                <h3>Recent Payments</h3>
                <div class="recent-list">
                    @foreach($recentPayments as $payment)
                        <div class="recent-item">
                            <h5>₹{{ number_format($payment->amount) }}</h5>
                            <p>{{ $payment->user->name }}</p>
                            <small>{{ $payment->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // User Registrations Chart
    const userRegCtx = document.getElementById('userRegistrationsChart').getContext('2d');
    const userRegChart = new Chart(userRegCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($userRegistrations)) !!},
            datasets: [{
                label: 'User Registrations',
                data: {!! json_encode(array_values($userRegistrations)) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Revenue Chart
    const revenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($monthlyRevenue)) !!},
            datasets: [{
                label: 'Revenue (₹)',
                data: {!! json_encode(array_values($monthlyRevenue)) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
