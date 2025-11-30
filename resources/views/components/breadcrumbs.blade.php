@props(['items' => []])

@if(count($items) > 0)
<nav aria-label="breadcrumb" class="breadcrumb-nav">
    <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a href="{{ route('home') }}" itemprop="item">
                <span itemprop="name">Home</span>
            </a>
            <meta itemprop="position" content="1" />
        </li>
        
        @foreach($items as $index => $item)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span itemprop="name">{{ $item['name'] }}</span>
                    <meta itemprop="position" content="{{ $index + 2 }}" />
                </li>
            @else
                <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a href="{{ $item['url'] }}" itemprop="item">
                        <span itemprop="name">{{ $item['name'] }}</span>
                    </a>
                    <meta itemprop="position" content="{{ $index + 2 }}" />
                </li>
            @endif
        @endforeach
    </ol>
</nav>
@endif
