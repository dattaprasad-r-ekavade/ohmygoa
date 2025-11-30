<div class="listing-card">
    <div class="list-img">
        @if($listing->featured_image)
            <img src="{{ asset('storage/' . $listing->featured_image) }}" alt="{{ $listing->title }}">
        @else
            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ $listing->title }}">
        @endif
        @if($listing->is_featured)
            <span class="featured-badge">Featured</span>
        @endif
    </div>
    <div class="list-con">
        <h4><a href="{{ route('listings.show', $listing->slug) }}">{{ $listing->title }}</a></h4>
        <p class="list-cat">
            <i class="material-icons">category</i>
            {{ $listing->category->name ?? 'N/A' }}
        </p>
        <p class="list-loc">
            <i class="material-icons">location_on</i>
            {{ $listing->location->name ?? 'N/A' }}
        </p>
        @if($listing->average_rating)
            <div class="list-rat">
                <span class="rating">{{ number_format($listing->average_rating, 1) }}</span>
                <i class="material-icons">star</i>
                <span class="reviews">({{ $listing->total_reviews }} reviews)</span>
            </div>
        @endif
        <div class="list-meta">
            <span><i class="material-icons">visibility</i> {{ $listing->views_count }}</span>
            <span><i class="material-icons">favorite</i> {{ $listing->bookmarks_count }}</span>
        </div>
    </div>
</div>
