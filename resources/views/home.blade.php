<x-layouts.app>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto md:px-4 sm:px-2 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Seaham Coastal Retreats
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Discover luxury coastal apartments in Seaham County Durham. Two stunning apartments overlooking the dramatic Seaham coastline,
                where industrial heritage meets natural beauty and seaglass treasures wash ashore daily.
            </p>
        </div>

        <!-- Hero Image Slider -->

        <x-image-slider />

        <h2 class="text-2xl md:text-4xl text-center font-bold text-gray-900 dark:text-white mt-12 mb-6">
            Explore Our Properties
        </h2>

        <!-- Properties Grid -->
        <div class="grid md:grid-cols-2 gap-12 lg:gap-16 mt-8">
            @foreach($venues as $venue)

                <x-venue-card
                    :venue="$venue"
                    :badge-text="$venue->badge_text"
                    :badge-color="$venue->theme_color"
                    :button-color="$venue->button_color ?? $venue->theme_color"
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
                        <img class="w-64 h-64 object-cover rounded-lg shadow-md" src="{{ url('storage/seaglass2.jpg') }}" alt="Seaham Seaglass" loading="lazy" width="256" height="256" decoding="async">
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Seaglass Hunting</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Seaham's world-famous seaglass beaches offer daily treasures. Hunt for rare blues, greens, and frosted whites along our unique coastline shaped by Victorian glass dumping.
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
                                The town's coastline is a treasure trove for beachcombers and photographers. Seaham Beach is famous for its sea glass, remnants of the town's glass bottle industry that have been polished by the waves into colorful gems. These unique finds have inspired a local craft movement, with artists creating jewelry and décor that celebrate Seaham's maritime legacy.
                                Beyond the shore, Seaham is embracing regeneration. New housing developments, improved transport links, and community initiatives are breathing fresh life into the town. Yet, it remains deeply connected to its past, with historic buildings like <a href="https://www.nationalchurchestrust.org/church/st-mary-virgin-seaham" class="font-bold hover:font-extrabold hover:text-blue-800 transition-all duration-200" target="_blank " rel="nofollow">St Mary the Virgin Church</a>, one of the oldest in the region, offering a glimpse into centuries of local history.
                            </p>
                        </div>

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
                                    <div class="border-l-4 border-purple-500 pl-4">
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
                                        <p class="text-gray-600 dark:text-gray-300 text-sm">Experience Seaham's vibrant community through events like the Seaham Food Festival and the annual Seaglass Festival.</p>
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
        <section id="contact" class="mt-20 bg-gradient-to-br from-blue-50 via-white to-slate-50 dark:from-gray-800 dark:via-gray-900 dark:to-slate-800 rounded-2xl p-4 lg:p-12 shadow-xl border border-blue-100 dark:border-gray-700">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-bold text-slate-800 dark:text-slate-200 mb-4">Get In Touch</h3>
                <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    Ready to plan your perfect coastal getaway? We're here to help make your Seaham retreat unforgettable.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <!-- Email Contact -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">Email Us</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Send us a message anytime</p>
                    <a href="mailto:booking@seahamretreats.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm transition-colors">
                        booking@seahamretreats.com
                    </a>
                </div>

                <!-- Phone Contact -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200 dark:border-gray-700">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">Call Us</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Available 9 AM - 8 PM daily</p>
                    <a href="tel:+441915270123" class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 font-medium text-sm transition-colors">
                        +44 191 527 0123
                    </a>
                </div>

                <!-- WhatsApp Contact -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg hover:shadow-xl transition-shadow border border-slate-200 dark:border-gray-700 md:col-span-2 lg:col-span-1">
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex items-center justify-center mb-4 mx-auto">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.465 3.488"/>
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">WhatsApp</h4>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Quick responses & booking help</p>
                    <a href="https://wa.me/441915270123" class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium text-sm transition-colors">
                        Message us on WhatsApp
                    </a>
                </div>
            </div>
            <livewire:contact-form>
            <!-- Additional Info -->
            <div class="text-center mt-8 pt-8 border-t border-slate-200 dark:border-gray-700">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    <strong>Business Hours:</strong> Monday - Sunday, 9:00 AM - 8:00 PM<br>
                    <strong>Response Time:</strong> We typically respond within 2 hours during business hours
                </p>
            </div>
        </section>

    </div>
</x-layouts.app>

