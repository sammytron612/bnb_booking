<x-layouts.app>
    <!-- Hero Image Slider -->
    <x-image-slider />

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Seaham Coastal Retreats
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Discover luxury coastal living in County Durham. Two stunning apartments overlooking the dramatic Seaham coastline,
                where industrial heritage meets natural beauty and seaglass treasures wash ashore daily.
            </p>
        </div>

        <!-- Properties Grid -->
        <div class="grid md:grid-cols-2 gap-12 lg:gap-16">

            <!-- The Light House -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <!-- Property Image Gallery -->
                <x-image-placeholder
                    title="The Light House"
                    badge="Premium Property"
                    badge-color="blue"
                    price="£120/night"
                />

                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">The Light House</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        A beacon of luxury overlooking Seaham's iconic lighthouse. This stunning coastal apartment features
                        panoramic sea views, modern amenities, and direct access to the famous seaglass beaches of the Heritage Coast.
                    </p>

                    <!-- Features -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            2 Bedrooms
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Sea Views
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Free Parking
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>
                            </svg>
                            Full Kitchen
                        </div>
                    </div>

                    <a href="{{route('light-house')}}" type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        View Details & Book
                    </a>
                </div>
            </div>

            <!-- Saras -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <!-- Property Image Gallery -->
                <x-image-placeholder
                    title="Sara's"
                    badge="Family Friendly"
                    badge-color="green"
                    price="£95/night"
                />

                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Saras</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Named after a family member, Saras offers a perfect blend of comfort and coastal charm.
                        Just a 2 minute drive from the famous seaglass beach and Tommy statue, ideal for families and couples alike.
                    </p>

                    <!-- Features -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                            </svg>
                            2 Bedrooms
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Seaglass Beach
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                            Garden Area
                        </div>
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            WiFi Included
                        </div>
                    </div>

                    <a href="{{route('saras')}}" type="button" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                        View Details & Book
                    </a>
                </div>
            </div>
        </div>

        <!-- About Seaham Section -->
        <div id="about" class="mt-20 text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                Why Choose Seaham?
            </h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/seaglass2.jpg') }}" alt="Seaham Seaglass">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Seaglass Hunting</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Seaham's world-famous seaglass beaches offer daily treasures. Hunt for rare blues, greens, and frosted whites along our unique coastline shaped by Victorian glass dumping.
                    </p>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/seahamcoastline.webp') }}" alt="Seaham Coastline">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Natural Beauty</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Stunning cliff walks, seaglass-studded beaches, and dramatic coastal views along the Durham Heritage Coast. Watch the waves tumble fresh seaglass daily onto our shores.
                    </p>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-center mx-auto mb-4">
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/durham.jpg') }}" alt="Durham City">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Perfect Location</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Easy access to Newcastle, Durham City, and the Northumberland coast. The best of both worlds.
                    </p>
                </div>
            </div>
            <section id="about" class="mt-20 bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl p-8 lg:p-12 shadow-lg">
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
                            <div class="text-gray-700 dark:text-gray-300 space-y-4 leading-relaxed">
                                <p>Nestled on the rugged Durham coastline, Seaham is a town steeped in history and resilience. Originally a quiet agricultural village, Seaham's transformation began in the early 19th century when the 3rd Marquess of Londonderry developed its harbour to support the booming coal industry. By 1845, coal mining had become the lifeblood of the town, shaping its identity and community spirit for generations.</p>

                                <p>Seaham also holds literary significance—<a class="font-bold hover:font-extrabold hover:text-blue-800 transition-all duration-200" href="https://en.wikipedia.org/wiki/Lord_Byron">Lord Byron</a> married Anne Isabella Milbanke at Seaham Hall in 1815, and their daughter Ada Lovelace, a pioneer of computing, was born from this union.</p>

                                <p>The town's industrial past is commemorated through landmarks like the <a href="https://www.thisisdurham.com/things-to-do/east-durham-heritage-and-lifeboat-centre-p722171" class="font-bold hover:font-extrabold hover:text-blue-800 transition-all duration-200">East Durham Heritage & Lifeboat Centre</a>, which honours the brave lifeboat crews and miners who shaped Seaham's legacy.</p>
                            </div>
                        </div>

                        <!-- Modern Seaham Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
                            <h3 class="text-2xl font-bold text-green-600 dark:text-green-400 mb-4 flex items-center justify-center">
                                Modern Seaham: Where Heritage Meets Coastal Beauty
                            </h3>
                            <p class="text-gray-700 text-center dark:text-gray-300 leading-relaxed">
                                Today, Seaham has reinvented itself as a vibrant seaside destination. Its regeneration has brought new life to the town, blending historical reverence with modern leisure.
                            </p>
                        </div>

                        <!-- Attractions Section -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-md">
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
                                </div>
                                <div class="space-y-4">
                                    <div class="border-l-4 border-red-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Tommy Statue</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">A powerful corten-steel sculpture by Ray Lonsdale, depicting a WWI soldier in the moment peace was declared. A moving tribute and one of the most photographed landmarks in the North East.</p>
                                    </div>
                                    <div class="border-l-4 border-yellow-500 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Local Festivals</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Experience Seaham's vibrant community through events like the Seaham Food Festival and the annual Seaglass Festival.</p>
                                    </div>
                                    <div class="border-l-4 border-yellow-900 pl-4">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">Seaham Hotel & Spa</h4>
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Indulge in luxury at the Seaham Hotel & Spa, offering stunning coastal views, a world-class spa, and fine dining experiences.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <section id="contact" class="mt-20 bg-gradient-to-br from-blue-50 to-white dark:from-gray-800 dark:to-gray-900 rounded-2xl p-8 lg:p-12 shadow-lg">
            <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-6">Contact Us</h3>
            <p class="text-gray-600 dark:text-gray-300">If you have any questions or inquiries, feel free to reach out to us!</p>
        </section>

    </div>
</x-layouts.app>

