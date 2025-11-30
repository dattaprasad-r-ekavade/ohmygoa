@extends('layouts.public')

@section('title', $query ? "Search: {$query}" : 'Search')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">
            @if($query)
                Search Results for "{{ $query }}"
            @else
                Search Directory
            @endif
        </h1>

        <!-- Search Form -->
        <form action="{{ route('search.index') }}" method="GET" class="bg-white rounded-lg shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Query -->
                <div class="md:col-span-2">
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}"
                           placeholder="What are you looking for?" 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           autocomplete="off"
                           id="search-input">
                    <div id="autocomplete-results" class="absolute bg-white border rounded-lg shadow-lg mt-1 hidden w-96 z-50"></div>
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="type" class="w-full px-4 py-2 border rounded-lg">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="listings" {{ $type === 'listings' ? 'selected' : '' }}>Business Listings</option>
                        <option value="events" {{ $type === 'events' ? 'selected' : '' }}>Events</option>
                        <option value="jobs" {{ $type === 'jobs' ? 'selected' : '' }}>Jobs</option>
                        <option value="products" {{ $type === 'products' ? 'selected' : '' }}>Products</option>
                        <option value="classifieds" {{ $type === 'classifieds' ? 'selected' : '' }}>Classifieds</option>
                    </select>
                </div>

                <!-- Search Button -->
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        Search
                    </button>
                </div>
            </div>

            <!-- Advanced Filters -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <select name="category_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="location_id" class="w-full px-4 py-2 border rounded-lg">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $locationId == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <input type="number" name="min_price" value="{{ $minPrice }}" placeholder="Min Price" 
                           class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <input type="number" name="max_price" value="{{ $maxPrice }}" placeholder="Max Price" 
                           class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div>
                    <select name="sort_by" class="w-full px-4 py-2 border rounded-lg">
                        <option value="relevance" {{ $sortBy === 'relevance' ? 'selected' : '' }}>Most Relevant</option>
                        <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="rating" {{ $sortBy === 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>
            </div>

            @auth
            <div class="mt-4">
                <button type="button" onclick="saveSearch()" class="text-blue-600 hover:text-blue-800 text-sm">
                    ⭐ Save this search
                </button>
            </div>
            @endauth
        </form>
    </div>

    @if($query)
        <!-- Results Summary -->
        <div class="mb-6">
            <p class="text-gray-600">Found <strong>{{ $totalResults }}</strong> results</p>
        </div>

        <!-- Results -->
        <div class="space-y-6">
            @if(!empty($results['listings']) && $results['listings']->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-4">Business Listings ({{ $results['listings']->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($results['listings'] as $listing)
                            @include('partials.listing-card', ['listing' => $listing])
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $results['listings']->links() }}
                    </div>
                </div>
            @endif

            @if(!empty($results['events']) && $results['events']->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-4">Events ({{ $results['events']->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($results['events'] as $event)
                            @include('partials.event-card', ['event' => $event])
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $results['events']->links() }}
                    </div>
                </div>
            @endif

            @if(!empty($results['jobs']) && $results['jobs']->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-4">Jobs ({{ $results['jobs']->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($results['jobs'] as $job)
                            @include('partials.job-card', ['job' => $job])
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $results['jobs']->links() }}
                    </div>
                </div>
            @endif

            @if(!empty($results['products']) && $results['products']->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-4">Products ({{ $results['products']->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        @foreach($results['products'] as $product)
                            @include('partials.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $results['products']->links() }}
                    </div>
                </div>
            @endif

            @if(!empty($results['classifieds']) && $results['classifieds']->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold mb-4">Classifieds ({{ $results['classifieds']->total() }})</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($results['classifieds'] as $classified)
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <h3 class="text-lg font-semibold">{{ $classified->title }}</h3>
                                <p class="text-gray-600 mt-2">{{ Str::limit($classified->description, 100) }}</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-lg font-bold text-green-600">₹{{ number_format($classified->price) }}</span>
                                    <span class="text-sm text-gray-500">{{ $classified->location->name ?? '' }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        {{ $results['classifieds']->links() }}
                    </div>
                </div>
            @endif
        </div>

        @if($totalResults === 0)
            <div class="text-center py-12">
                <p class="text-xl text-gray-600 mb-4">No results found for "{{ $query }}"</p>
                <p class="text-gray-500">Try different keywords or remove some filters</p>
            </div>
        @endif
    @endif

    <!-- Saved Searches Sidebar -->
    @auth
    @if($savedSearches->count() > 0)
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Your Saved Searches</h3>
        <ul class="space-y-2">
            @foreach($savedSearches as $saved)
                <li>
                    <a href="{{ $saved->search_url }}" class="text-blue-600 hover:underline">
                        {{ $saved->name }}
                    </a>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('search.saved') }}" class="text-sm text-blue-600 hover:underline mt-4 block">
            View all saved searches →
        </a>
    </div>
    @endif
    @endauth
</div>

@push('scripts')
<script>
// Autocomplete functionality
const searchInput = document.getElementById('search-input');
const autocompleteResults = document.getElementById('autocomplete-results');
let debounceTimer;

searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const query = this.value;

    if (query.length < 2) {
        autocompleteResults.classList.add('hidden');
        return;
    }

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('search.autocomplete') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.suggestions.length > 0) {
                    autocompleteResults.innerHTML = data.suggestions.map(item => 
                        `<a href="${item.url}" class="block px-4 py-2 hover:bg-gray-100">
                            <span class="text-xs text-gray-500">${item.type}</span><br>
                            ${item.title}
                        </a>`
                    ).join('');
                    autocompleteResults.classList.remove('hidden');
                } else {
                    autocompleteResults.classList.add('hidden');
                }
            });
    }, 300);
});

// Save search functionality
function saveSearch() {
    const name = prompt('Enter a name for this search:');
    if (!name) return;

    const formData = new FormData(document.querySelector('form'));
    const filters = {};
    for (const [key, value] of formData.entries()) {
        if (key !== 'q' && value) filters[key] = value;
    }

    fetch('{{ route('search.save') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: name,
            query: formData.get('q'),
            filters: filters
        })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        location.reload();
    });
}

// Close autocomplete when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !autocompleteResults.contains(e.target)) {
        autocompleteResults.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
