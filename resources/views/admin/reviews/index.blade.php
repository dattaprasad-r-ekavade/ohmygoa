@extends('layouts.admin')

@section('title', 'Reviews Management')

@section('content')
<div class="container-fluid px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Reviews Management</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <select name="status" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <select name="rating" class="w-full px-4 py-2 border rounded-lg">
                    <option value="">All Ratings</option>
                    <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4 Stars</option>
                    <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3 Stars</option>
                    <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2 Stars</option>
                    <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1 Star</option>
                </select>
            </div>
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search reviews..." class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filter
                </button>
            </div>
            <div>
                <a href="{{ route('admin.reviews.index') }}" class="block w-full text-center bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">{{ $review->user->name }}</h3>
                        <div class="flex items-center mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">★</span>
                            @endfor
                            <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-700">{{ $review->review }}</p>
                        <div class="mt-2 text-sm text-gray-500">
                            For: <a href="#" class="text-blue-600 hover:underline">{{ $review->reviewable->title ?? $review->reviewable->name }}</a>
                        </div>
                    </div>
                </div>
                <div>
                    <span class="px-3 py-1 text-sm rounded-full 
                        {{ $review->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $review->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $review->status === 'rejected' ? 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($review->status) }}
                    </span>
                </div>
            </div>
            <div class="flex gap-2 mt-4 pt-4 border-t">
                @if($review->status === 'pending')
                    <button onclick="approveReview({{ $review->id }})" 
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Approve
                    </button>
                    <button onclick="rejectReview({{ $review->id }})" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Reject
                    </button>
                @endif
                <button onclick="deleteReview({{ $review->id }})" 
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    Delete
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-md p-6 text-center text-gray-500">
            No reviews found
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</div>

@push('scripts')
<script>
function approveReview(id) {
    if (!confirm('Approve this review?')) return;
    
    fetch(`/admin/reviews/${id}/approve`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => alert('Error approving review'));
}

function rejectReview(id) {
    if (!confirm('Reject this review?')) return;
    
    fetch(`/admin/reviews/${id}/reject`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => alert('Error rejecting review'));
}

function deleteReview(id) {
    if (!confirm('Delete this review permanently?')) return;
    
    fetch(`/admin/reviews/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => alert('Error deleting review'));
}
</script>
@endpush
@endsection
