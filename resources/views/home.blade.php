@php
    $featuredImage = $venues->first()?->propertyImages?->where('featured', true)?->first();
    $seoData = [
        'title' => 'Luxury Coastal Holiday Rentals in Seaham',
        'description' => 'Discover luxury coastal apartments in Seaham County Durham. Two stunning apartments minutes from the dramatic Seaham coastline where seaglass treasures wash ashore daily.',
        'keywords' => 'Seaham holiday rentals, coastal accommodation, seaside holidays, luxury holiday homes, Durham coast, sea views, vacation rentals, Seaglass, Sea Glass',
        'type' => 'website',
        'image' => $featuredImage ? $featuredImage->secure_url : null,
        'imageAlt' => 'Seaham Coastal Retreats - Luxury Holiday Accommodation'
    ];
@endphp

<x-layouts.app :seoData="$seoData">

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto md:px-4 sm:px-2 lg:px-4 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Seaham Coastal Retreats
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Discover luxury coastal apartments in Seaham County Durham. Two stunning apartments minutes from the dramatic Seaham coastline,
                where industrial heritage meets natural beauty and seaglass treasures wash ashore daily.
            </p>
        </div>

        <!-- Hero Image Slider -->

        <x-image-slider />

        <h2 class="text-2xl md:text-4xl text-center font-bold text-gray-900 dark:text-white mt-12 mb-6">
            Explore Our Properties
        </h2>

        <!-- Properties Grid -->
        <div class="grid md:grid-cols-2 gap-12 lg:gap-16 mt-8 lg:px-4">
            @foreach($venues as $venue)

                <x-venue-card
                    :venue="$venue"
                    :badge-text="$venue->badge_text"
                    :badge-color="$venue->theme_color"
                    :button-color="$venue->theme_color"
                    :route="$venue->route"
                />
            @endforeach
        </div>

        <!-- About Seaham Section -->
        <div id="about" class="mt-20 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                Why Choose Seaham?
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-2 md:p-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/seahamharbour.jpg') }}" alt="Seaham Harbour" loading="lazy" width="256" height="256" decoding="async">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Seaham Harbour</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        A charming Victorian harbour built in 1828 by the 3rd Marquess of Londonderry to export coal from local mines. Today, this picturesque harbour offers stunning coastal walks, traditional fishing boats.
                    </p>
                </div>
                <div class="p-2 md:p-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/seahamcoastline.webp') }}" alt="Seaham Coastline" loading="lazy" width="256" height="256" decoding="async">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Natural Beauty</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Stunning cliff walks, seaglass-studded beaches, and dramatic coastal views along the Durham Heritage Coast. Watch the waves tumble fresh seaglass daily onto our shores.
                    </p>
                </div>
                <div class="p-2 md:p-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/durham.jpg') }}" alt="Durham City" loading="lazy" width="256" height="256" decoding="async">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Perfect Location</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Easy access to Newcastle, Durham City, and the Northumberland coast. The best of both worlds.
                    </p>
                </div>
            </div>
            <section id="about" class="mt-20 bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl p-4 lg:p-12 shadow-lg">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 text-center">
                            A Coastal Gem of History and Charm
                        </h2>
                        <div class="w-24 h-1 bg-blue-500 mx-auto rounded-full"></div>
                    </div>

                    <div class="space-y-8">
                        <!-- Heritage Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                            <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-4 flex items-center justify-center">
                                Discover Seaham's Rich Heritage
                            </h3>
                            <div class="text-gray-700 dark:text-gray-300 space-y-4 leading-relaxed text-center md:text-left">
                                <img class="block mx-auto mb-4 md:float-left md:mt-1 w-64 h-64 object-cover rounded-lg shadow-md md:mr-6 md:mb-4" src="{{ url('storage/knackpit.jpg') }}" alt="The knackpit in Seaham" loading="lazy" width="256" height="256" decoding="async">
                                <p>Nestled on the rugged Durham coastline, Seaham is a town steeped in history and resilience. Originally a quiet agricultural village, Seaham's transformation began in the early 19th century when the 3rd Marquess of Londonderry developed its harbour to support the booming coal industry. By 1845, coal mining had become the lifeblood of the town, shaping its identity and community spirit for generations.</p>

                                <p>Seaham also holds literary significance—<a class="font-bold hover:font-extrabold hover:text-blue-800 transition-all duration-200" href="https://en.wikipedia.org/wiki/Lord_Byron" target="_blank " rel="nofollow">Lord Byron</a> married Anne Isabella Milbanke at Seaham Hall in 1815, and their daughter Ada Lovelace, a pioneer of computing, was born from this union.</p>

                                <p>The town's industrial past is commemorated through landmarks like the <a href="https://www.thisisdurham.com/things-to-do/east-durham-heritage-and-lifeboat-centre-p722171" class="font-bold hover:font-extrabold hover:text-blue-800 transition-all duration-200" target="_blank " rel="nofollow">East Durham Heritage & Lifeboat Centre</a>, which honours the brave lifeboat crews and miners who shaped Seaham's legacy.</p>
                            </div>
                        </div>

                        <!-- Modern Seaham Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-6 shadow-md">
                            <h3 class="text-2xl font-bold text-green-600 dark:text-green-400 mb-4 flex items-center justify-center">
                                Modern Seaham: Where Heritage Meets Coastal Beauty
                            </h3>
                            <p class="text-gray-700 text-center md:text-left dark:text-gray-300 leading-relaxed">
                                <img class="block mx-auto mb-4 md:float-left md:mt-1 w-64 h-64 object-cover rounded-lg shadow-md md:mr-6 md:mb-4" src="{{ url('storage/tommy222.webp') }}" alt="The knackpit in Seaham">
                                With ongoing regeneration projects and a growing arts scene, Seaham is becoming a hub for creativity and community spirit. The town's transformation includes beautiful coastal walks, independent cafes serving locally-sourced produce, and artisan shops celebrating the area's maritime heritage. Visitors can enjoy the award-winning Blue Flag beaches, explore the newly developed town center with its mix of historic architecture and contemporary amenities, or take part in the vibrant festival calendar that brings the community together throughout the year. <p>The famous weekly markets showcase local crafts, fresh seafood, and regional specialties, while the nearby countryside offers excellent walking and cycling routes through rolling farmland and along dramatic clifftops.</p> <p>Whether you're exploring its rich industrial history, hunting for rare seaglass treasures, or simply enjoying the therapeutic sound of waves against limestone cliffs, Seaham invites visitors to experience a unique blend of tradition and tranquility, making it a true gem of the North East coast.</p>
                            </p>
                        </div>

                        <!-- Seaham's Seaglass Section -->
                        <!--<div class="bg-white dark:bg-gray-800 rounded-xl p-4 md:p-6 shadow-md">
                            <h3 class="text-2xl font-bold text-teal-600 dark:text-teal-400 mb-4 flex items-center justify-center">
                                Seaham's Famous Seaglass: Nature's Hidden Treasure
                            </h3>
                            <p class="text-gray-700 text-center md:text-left dark:text-gray-300 leading-relaxed">
                                <img class="block mx-auto mb-4 md:float-left md:mt-1 w-64 h-64 object-cover rounded-lg shadow-md md:mr-6 md:mb-4" src="{{ url('storage/seaglass2.jpg') }}" alt="Seaham seaglass collection" loading="lazy" width="256" height="256" decoding="async">
                                Seaham's coastline is world-renowned for producing some of the finest seaglass on Earth. This natural phenomenon began in the Victorian era when the Seaham Bottle Works dumped millions of glass bottles directly into the North Sea. Over the past 150 years, the relentless waves have transformed this industrial waste into smooth, frosted gems that wash ashore daily.
                                What makes Seaham seaglass truly special is its incredible variety and rarity. Collectors travel from around the globe to hunt for the coveted <strong>Seaham blues</strong> - rare pieces from Victorian poison bottles and Vicks VapoRub jars. The famous <strong>cornflower blue</strong> and <strong>codd bottle blue</strong> are among the most sought-after colors in the seaglass collecting world.
                                Beyond the blues, Seaham's beaches yield an rainbow of colors including emerald greens from beer bottles, pure whites from milk glass, and occasional reds from ship's lanterns. Each piece tells a story of Seaham's industrial heritage, polished to perfection by decades of tumbling in the North Sea. Join the daily treasure hunters at dawn for the best finds, when fresh seaglass is revealed by the receding tide.
                            </p>
                        </div>
                        -->

                        <!-- Attractions Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-2 md:p-6 shadow-md">
                            <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-6 flex items-center justify-center">
                                Top Attractions in Seaham
                            </h3>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-4">
                                    <div class="border-l-4 border-blue-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Seaham Beach</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Famous for its unique seaglass, the beach is a haven for collectors and nature lovers alike.</p>
                                    </div>
                                    <div class="border-l-4 border-green-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Seaham Harbour</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">A picturesque spot perfect for leisurely strolls, fishing, and enjoying fresh seafood at local eateries.</p>
                                    </div>
                                    <div class="border-l-4 border-purple-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Durham Heritage Coast</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Explore stunning cliff walks and breathtaking views along this designated Area of Outstanding Natural Beauty.</p>
                                    </div>
                                    <div class="border-l-4 border-teal-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">St Mary the Virgin Church</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">
One of the most outstanding features in Seaham is St. Mary the Virgin Church. Located to the north of Seaham this old Anglo Saxon church has roots thought to date back to the 7th century. The Church is recognised as one of the 20 oldest surviving churches in the whole country.</p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="border-l-4 border-red-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Tommy Statue</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">A powerful corten-steel sculpture by Ray Lonsdale, depicting a WWI soldier in the moment peace was declared. A moving tribute and one of the most photographed landmarks in the North East.</p>
                                    </div>
                                    <div class="border-l-4 border-yellow-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Local Festivals</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Experience Seaham's vibrant community through events like the Seaham Food Festival.</p>
                                    </div>
                                    <div class="border-l-4 border-yellow-900 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Seaham Hotel & Spa</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Indulge in luxury at the Seaham Hotel & Spa, offering stunning coastal views, a world-class spa, and fine dining experiences.</p>
                                    </div>
                                    <div class="border-l-4 border-yellow-300 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Dalton Park Designer Outlet</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">A 5 minute drive from Seaham, this outlet offers a range of designer brands at discounted prices.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>

