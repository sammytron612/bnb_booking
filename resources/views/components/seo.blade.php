@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => null,
    'robots' => 'index, follow',
    'image' => null,
    'imageAlt' => null,
    'type' => 'website',
    'author' => null,
    'published' => null,
    'modified' => null,
    'venue' => null,
    'reviews' => null,
    'price' => null,
    'availability' => null
])

@php
    // Extract individual values from props (direct access)
    $seoTitle = $title;
    $seoDescription = $description;
    $seoKeywords = $keywords;
    $seoCanonical = $canonical ?? request()->url();
    $seoImage = $image;
    $seoImageAlt = $imageAlt;
    $seoVenue = $venue;
    $seoReviews = $reviews;
    $seoPrice = $price;

    // Debug: Check if description is being passed
    // \Log::info('SEO Debug: $description = ' . ($description ?? 'NULL'));
    // \Log::info('SEO Debug: $seoVenue description = ' . ($seoVenue?->description2 ?? 'NULL'));

    // Default values
    $siteName = 'Seaham Coastal Retreats';
    $defaultDescription = 'Luxury coastal holiday rentals in Seaham. Book The Light House or Saras for your perfect seaside getaway. Stunning sea views, modern amenities, and unforgettable experiences.';

    // Final values with fallbacks - prioritize venue description if available
    $fullTitle = $seoTitle ? $seoTitle . ' | ' . $siteName : $siteName;
    $finalDescription = $seoDescription ?? ($seoVenue?->description2 ?? $seoVenue?->description1 ?? $defaultDescription);
    $finalKeywords = $seoKeywords ?? 'Seaham holiday rentals, coastal accommodation, seaside holidays, lighthouse rental, luxury holiday homes, Durham coast, sea views, vacation rentals, sea glass';
    $finalImage = $seoImage ?? asset('apple-touch-icon.svg');
    $finalImageAlt = $seoImageAlt ?? $siteName . ' Logo';

    // Business information
    $businessInfo = [
        'name' => $siteName,
        'phone' => env('OWNER_PHONE_NO', '+44 1234 567890'),
        'email' => env('OWNER_EMAIL', env('MAIL_FROM_ADDRESS', 'info@seahamcoastalretreats.com')),
        'address' => [
            'street' => 'Seaham',
            'city' => 'Seaham',
            'region' => 'County Durham',
            'postalCode' => 'SR7',
            'country' => 'United Kingdom'
        ],
        'website' => config('app.url'),
        'priceRange' => '££'
    ];

    // Generate structured data
    $structuredData = [];

    // Organization schema
    $structuredData[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $businessInfo['name'],
        'url' => $businessInfo['website'],
        'logo' => asset('favicon.svg'),
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => $businessInfo['phone'],
            'contactType' => 'Customer Service',
            'email' => $businessInfo['email']
        ],
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $businessInfo['address']['city'],
            'addressRegion' => $businessInfo['address']['region'],
            'postalCode' => $businessInfo['address']['postalCode'],
            'addressCountry' => $businessInfo['address']['country']
        ],
        'sameAs' => [
            // Add social media URLs when available
        ]
    ];

    // Website schema
    $structuredData[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => $businessInfo['website'],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => $businessInfo['website'] . '/?search={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];

    // Venue-specific schema
    if ($seoVenue) {
        $venueImages = [];
        if ($seoVenue->propertyImages) {
            foreach ($seoVenue->propertyImages as $img) {
                // Only add images that have valid location (image path)
                if (!empty($img->location)) {
                    // Remove leading slash and use asset() to generate proper URL
                    $venueImages[] = asset(ltrim($img->location, '/'));
                }
            }
        }

        // If no images found, add a fallback image
        if (empty($venueImages)) {
            $venueImages[] = asset('apple-touch-icon.svg');
        }

        $venueSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'LodgingBusiness',
            'name' => $seoVenue->venue_name,
            'description' => $seoVenue->description2 ?? $seoVenue->description1,
            'url' => route('venue.show', $seoVenue->route),
            'image' => $venueImages,
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Seaham',
                'addressRegion' => 'County Durham',
                'postalCode' => $seoVenue->postcode ?? 'SR7',
                'addressCountry' => 'United Kingdom'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $seoVenue->latitude ?? 54.8386, // Venue coordinates or Seaham fallback
                'longitude' => $seoVenue->longitude ?? -1.3429
            ],
            'priceRange' => '££',
            'amenityFeature' => []
        ];

        // Add amenities to schema
        if ($seoVenue->amenities) {
            foreach ($seoVenue->amenities as $amenity) {
                $venueSchema['amenityFeature'][] = [
                    '@type' => 'LocationFeatureSpecification',
                    'name' => $amenity->title
                ];
            }
        }

        // Add price if available
        if ($seoVenue->price) {
            $venueSchema['offers'] = [
                '@type' => 'Offer',
                'price' => $seoVenue->price,
                'priceCurrency' => 'GBP',
                'availability' => 'https://schema.org/InStock'
            ];
        }

        // Add reviews if available
        if ($seoReviews && $seoReviews->count() > 0) {
            $venueSchema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round($seoReviews->avg('rating'), 1),
                'reviewCount' => $seoReviews->count(),
                'bestRating' => '5',
                'worstRating' => '1'
            ];

            $reviewSchemas = [];
            foreach ($seoReviews->take(5) as $review) {
                $reviewSchemas[] = [
                    '@type' => 'Review',
                    'author' => [
                        '@type' => 'Person',
                        'name' => $review->name ?? $review->guest_name ?? 'Guest'
                    ],
                    'reviewRating' => [
                        '@type' => 'Rating',
                        'ratingValue' => $review->rating,
                        'bestRating' => '5',
                        'worstRating' => '1'
                    ],
                    'reviewBody' => $review->review ?? $review->comment ?? '',
                    'datePublished' => $review->created_at ? $review->created_at->toISOString() : null
                ];
            }
            $venueSchema['review'] = $reviewSchemas;
        }

        $structuredData[] = $venueSchema;
    }
@endphp

<!-- Basic Meta Tags -->
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $finalDescription }}">
<meta name="keywords" content="{{ $finalKeywords }}">
<meta name="robots" content="{{ $robots }}">
<meta name="author" content="{{ $author ?? $siteName }}">
<link rel="canonical" href="{{ $seoCanonical }}">

<!-- Open Graph Meta Tags -->
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $seoTitle ?? $siteName }}">
<meta property="og:description" content="{{ $finalDescription }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $finalImage }}">
<meta property="og:image:alt" content="{{ $finalImageAlt }}">
<meta property="og:locale" content="en_GB">

@if($seoVenue)
<meta property="og:type" content="website">
<meta property="place:location:latitude" content="{{ $seoVenue->latitude ?? 54.8386 }}">
<meta property="place:location:longitude" content="{{ $seoVenue->longitude ?? -1.3429 }}">
@endif

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle ?? $siteName }}">
<meta name="twitter:description" content="{{ $finalDescription }}">
<meta name="twitter:image" content="{{ $finalImage }}">
<meta name="twitter:image:alt" content="{{ $finalImageAlt }}">

<!-- Additional Meta Tags -->
<meta name="theme-color" content="#2563eb">
<meta name="msapplication-TileColor" content="#2563eb">

@if($published)
<meta property="article:published_time" content="{{ $published }}">
@endif

@if($modified)
<meta property="article:modified_time" content="{{ $modified }}">
@endif

<!-- Venue-specific meta tags -->
@if($seoVenue)
<meta property="business:contact_data:locality" content="Seaham">
<meta property="business:contact_data:region" content="County Durham">
<meta property="business:contact_data:postal_code" content="{{ $seoVenue->postcode ?? 'SR7' }}">
<meta property="business:contact_data:country_name" content="United Kingdom">
@if($seoVenue->price)
<meta property="product:price:amount" content="{{ $seoVenue->price }}">
<meta property="product:price:currency" content="GBP">
@endif
@endif

<!-- Search Engine Verification Tags -->
{{-- <meta name="google-site-verification" content="YOUR_GOOGLE_VERIFICATION_CODE"> --}}
{{-- <meta name="msvalidate.01" content="YOUR_BING_VERIFICATION_CODE"> --}}
{{-- <meta name="yandex-verification" content="YOUR_YANDEX_VERIFICATION_CODE"> --}}

<!-- Google Analytics 4 -->
@if(config('app.env') === 'production' && config('services.google.analytics_id'))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.analytics_id') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{{ config('services.google.analytics_id') }}', {
    enhanced_measurement: true,
    anonymize_ip: true,
    cookie_flags: 'SameSite=None;Secure'
  });

  @if($seoVenue)
  // Track venue page view
  gtag('event', 'venue_view', {
    venue_name: '{{ $seoVenue->venue_name }}',
    venue_id: {{ $seoVenue->id }},
    @if($seoVenue->price)
    price_per_night: {{ $seoVenue->price }}
    @endif
  });
  @endif
</script>
@endif

<!-- Structured Data (JSON-LD) -->
@foreach($structuredData as $schema)
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endforeach
