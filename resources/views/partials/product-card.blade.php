<div class="product-card">
    <div class="product-img">
        @if($product->featured_image)
            <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}">
        @else
            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ $product->name }}">
        @endif
        @if($product->is_featured)
            <span class="featured-badge">Featured</span>
        @endif
        @if($product->discount_percentage > 0)
            <span class="discount-badge">-{{ $product->discount_percentage }}%</span>
        @endif
    </div>
    <div class="product-con">
        <h4><a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a></h4>
        <p class="product-cat">{{ $product->category->name ?? 'N/A' }}</p>
        <div class="product-price">
            @if($product->sale_price && $product->sale_price < $product->regular_price)
                <span class="sale-price">₹{{ number_format($product->sale_price) }}</span>
                <span class="regular-price">₹{{ number_format($product->regular_price) }}</span>
            @else
                <span class="price">₹{{ number_format($product->regular_price) }}</span>
            @endif
        </div>
        @if($product->average_rating)
            <div class="product-rating">
                <span class="rating">{{ number_format($product->average_rating, 1) }}</span>
                <i class="material-icons">star</i>
                <span class="reviews">({{ $product->total_reviews }})</span>
            </div>
        @endif
        @if($product->stock_quantity <= 0)
            <span class="out-of-stock">Out of Stock</span>
        @elseif($product->stock_quantity < 10)
            <span class="low-stock">Only {{ $product->stock_quantity }} left</span>
        @endif
    </div>
</div>
