@props(['meta' => [], 'structuredData' => null])

{{-- Basic Meta Tags --}}
<title>{{ $meta['title'] ?? config('app.name') }}</title>
<meta name="description" content="{{ $meta['description'] ?? '' }}">
@if(isset($meta['canonical']))
<link rel="canonical" href="{{ $meta['canonical'] }}">
@endif

{{-- Open Graph Tags --}}
<meta property="og:title" content="{{ $meta['og_title'] ?? $meta['title'] ?? config('app.name') }}">
<meta property="og:description" content="{{ $meta['og_description'] ?? $meta['description'] ?? '' }}">
<meta property="og:type" content="{{ $meta['og_type'] ?? 'website' }}">
<meta property="og:url" content="{{ $meta['og_url'] ?? url()->current() }}">
@if(isset($meta['og_image']))
<meta property="og:image" content="{{ $meta['og_image'] }}">
<meta property="og:image:alt" content="{{ $meta['og_title'] ?? $meta['title'] ?? '' }}">
@endif
<meta property="og:site_name" content="{{ $meta['og_site_name'] ?? config('app.name') }}">
<meta property="og:locale" content="en_US">

{{-- Twitter Card Tags --}}
<meta name="twitter:card" content="{{ $meta['twitter_card'] ?? 'summary_large_image' }}">
<meta name="twitter:title" content="{{ $meta['twitter_title'] ?? $meta['title'] ?? config('app.name') }}">
<meta name="twitter:description" content="{{ $meta['twitter_description'] ?? $meta['description'] ?? '' }}">
@if(isset($meta['twitter_image']))
<meta name="twitter:image" content="{{ $meta['twitter_image'] }}">
@endif

{{-- Additional Meta Tags --}}
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<meta name="googlebot" content="index, follow">

{{-- Structured Data (JSON-LD) --}}
@if($structuredData)
<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
