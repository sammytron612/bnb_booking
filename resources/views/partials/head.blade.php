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

    <!-- Breadcrumb Structured Data (only) -->
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

<!-- Performance Resource Hints -->
<link rel="preconnect" href="https://js.stripe.com">
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
<link rel="preconnect" href="https://www.google-analytics.com">
<link rel="dns-prefetch" href="//maps.googleapis.com">
<link rel="dns-prefetch" href="//fonts.bunny.net">
<link rel="dns-prefetch" href="//www.googletagmanager.com">

<!-- Critical CSS Inline (Above the fold styles) -->
<style>
/* Critical above-the-fold styles */
.min-h-screen{min-height:100vh}.bg-white{background-color:#fff}.flex{display:flex}.flex-col{flex-direction:column}
.bg-gradient-to-r{background-image:linear-gradient(to right,var(--tw-gradient-stops))}
.from-slate-50{--tw-gradient-from:#f8fafc;--tw-gradient-to:rgb(248 250 252 / 0);--tw-gradient-stops:var(--tw-gradient-from),var(--tw-gradient-to)}
.via-blue-50{--tw-gradient-to:rgb(239 246 255 / 0);--tw-gradient-stops:var(--tw-gradient-from),#eff6ff,var(--tw-gradient-to)}
.to-slate-100{--tw-gradient-to:#f1f5f9}
.border-b{border-bottom-width:1px}.border-gray-200{border-color:#e5e7eb}
.shadow-xl{box-shadow:0 20px 25px -5px rgb(0 0 0 / 0.1),0 8px 10px -6px rgb(0 0 0 / 0.1)}
.py-4{padding-top:1rem;padding-bottom:1rem}.z-50{z-index:50}
</style>

<!-- Font Loading with Performance Optimization -->
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap" rel="stylesheet"></noscript>

<!-- Deferred CSS and JS Loading -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- @livewireStyles --}}
{{-- @fluxAppearance --}}
