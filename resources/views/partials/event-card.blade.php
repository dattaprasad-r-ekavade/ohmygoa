<div class="event-card">
    <div class="event-img">
        @if($event->featured_image)
            <img src="{{ asset('storage/' . $event->featured_image) }}" alt="{{ $event->title }}">
        @else
            <img src="{{ asset('images/placeholder.jpg') }}" alt="{{ $event->title }}">
        @endif
        <div class="event-date-badge">
            <span class="day">{{ $event->start_date->format('d') }}</span>
            <span class="month">{{ $event->start_date->format('M') }}</span>
        </div>
    </div>
    <div class="event-con">
        <h4><a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a></h4>
        <p class="event-loc">
            <i class="material-icons">location_on</i>
            {{ $event->location->name ?? $event->venue }}
        </p>
        <p class="event-time">
            <i class="material-icons">schedule</i>
            {{ $event->start_date->format('M d, Y h:i A') }}
        </p>
        @if($event->is_free)
            <span class="event-price free">Free Event</span>
        @elseif($event->entry_fee)
            <span class="event-price">₹{{ number_format($event->entry_fee) }}</span>
        @endif
        <div class="event-meta">
            <span><i class="material-icons">visibility</i> {{ $event->views_count }}</span>
            <span><i class="material-icons">people</i> {{ $event->attendees_count }} attending</span>
        </div>
    </div>
</div>
