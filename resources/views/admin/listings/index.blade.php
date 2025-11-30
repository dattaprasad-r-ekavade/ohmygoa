@extends('layouts.admin')

@section('title', 'Business Listings')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Business Listings</h1>
        <a href="{{ route('admin.listings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Add New Listing
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.listings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search listings..." class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <select name="category" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filter
                </button>
            </div>
            <div>
                <a href="{{ route('admin.listings.index') }}" class="block w-full text-center bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-gray-500 text-sm">Total Listings</div>
            <div class="text-3xl font-bold">{{ $stats['total'] ?? 0 }}</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-md p-6">
            <div class="text-yellow-700 text-sm">Pending Approval</div>
            <div class="text-3xl font-bold text-yellow-700">{{ $stats['pending'] ?? 0 }}</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow-md p-6">
            <div class="text-green-700 text-sm">Approved</div>
            <div class="text-3xl font-bold text-green-700">{{ $stats['approved'] ?? 0 }}</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-md p-6">
            <div class="text-blue-700 text-sm">Featured</div>
            <div class="text-3xl font-bold text-blue-700">{{ $stats['featured'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listing</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stats</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($listings as $listing)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($listing->featured_image)
                                <img src="{{ asset('storage/' . $listing->featured_image) }}" 
                                     alt="{{ $listing->title }}" class="h-12 w-12 rounded object-cover">
                            @else
                                <div class="h-12 w-12 rounded bg-gray-200"></div>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $listing->title }}
                                    @if($listing->is_featured)
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Featured</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">{{ $listing->location->name ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $listing->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $listing->category->name ?? '' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $listing->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $listing->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $listing->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($listing->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ⭐ {{ number_format($listing->average_rating, 1) }} ({{ $listing->total_reviews }})<br>
                        👁️ {{ $listing->views_count }} views
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('listings.show', $listing->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-900">View</a>
                        <a href="{{ route('admin.listings.edit', $listing) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        @if($listing->status === 'pending')
                            <form action="{{ route('admin.listings.approve', $listing) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.listings.destroy', $listing) }}" method="POST" class="inline" 
                              onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No listings found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $listings->links() }}
    </div>
</div>
@endsection
