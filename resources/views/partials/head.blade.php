<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- SEO Component -->
@if(isset($seoData))
    <x-seo
        :title="$seoData['title'] ?? null"
        :description="$seoData['description'] ?? null"
        :keywords="$seoData['keywords'] ?? null"
        :canonical="$seoData['canonical'] ?? null"
        :robots="$seoData['robots'] ?? 'index, follow'"
        :image="$seoData['image'] ?? null"
        :image-alt="$seoData['imageAlt'] ?? null"
        :image-width="$seoData['imageWidth'] ?? null"
        :image-height="$seoData['imageHeight'] ?? null"
        :type="$seoData['type'] ?? 'website'"
        :venue="$seoData['venue'] ?? null"
        :reviews="$seoData['reviews'] ?? null"
        :price="$seoData['price'] ?? null"
        :rating="$seoData['rating'] ?? null"
        :review-count="$seoData['reviewCount'] ?? null"
        :address="$seoData['address'] ?? null"
        :coordinates="$seoData['coordinates'] ?? null"
        :structured-data="$seoData['structuredData'] ?? null"
        :breadcrumb-data="$seoData['breadcrumbData'] ?? null"
    />

    <!-- Enhanced Structured Data -->
    @if(isset($seoData['structuredData']))
    <script type="application/ld+json">
    {!! json_encode($seoData['structuredData'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif

    @if(isset($seoData['breadcrumbData']))
    <script type="application/ld+json">
    {!! json_encode($seoData['breadcrumbData'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @endif
@else
    <title>{{ $title ?? config('app.name') }}</title>
    <x-seo />
@endif

<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="icon" href="/favicon.ico" sizes="32x32">
<link rel="apple-touch-icon" href="/apple-touch-icon.svg" sizes="180x180">

<!-- Resource Hints for Performance -->
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

<!-- Font Loading with Performance Optimization -->
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet"></noscript>

@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- @livewireStyles --}}
{{-- @fluxAppearance --}}
