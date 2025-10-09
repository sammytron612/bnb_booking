<x-layouts.app :seoData="$seoData">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-800 dark:text-slate-200 mb-4">Seaglass</h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                Discover the natural treasure of Seaham's coastline - beautiful seaglass washed up by the North Sea, creating unique gems perfect for collectors and beach enthusiasts.
            </p>
        </div>

        <!-- Historical Section -->
        <div class="mb-12">
            <div class="bg-gradient-to-r from-slate-50 via-blue-50 to-slate-50 dark:from-slate-800 dark:via-blue-900/20 dark:to-slate-800 rounded-xl p-8 mb-8">
                <h2 class="text-3xl font-bold text-slate-800 dark:text-slate-200 mb-6 text-center">The History of Seaham Seaglass</h2>

                <!-- Historical Image -->
                <div class="mb-8 text-center">
                    <div class="relative inline-block">
                        <img src="{{ asset('storage/seaham_bottle_works1.jpg') }}"
                             alt="Historic Seaham Bottle Works - Victorian glass factory that created the seaglass legacy"
                             class="rounded-xl shadow-lg max-w-full h-auto mx-auto border-4 border-white dark:border-gray-700"
                             style="max-height: 400px;">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent rounded-b-xl p-4">
                            <p class="text-white text-sm font-medium">Historic Seaham Bottle Works (1853-1921)</p>
                            <p class="text-white/80 text-xs">The Victorian glass factory that created Seaham's seaglass legacy</p>
                        </div>
                    </div>
                </div>

                <div class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-3">Victorian Glass Heritage</h3>
                                <p class="text-slate-600 dark:text-slate-400">
                                    Seaham's seaglass story begins in the Victorian era with the establishment of Seaham Bottle Works in 1853. This industrial glass factory was founded by the renowned Candlish family and became one of the largest bottle manufacturers in the North East of England.
                                </p>
                            </div>

                            <div>
                                <h3 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-3">The Candlish Legacy</h3>
                                <p class="text-slate-600 dark:text-slate-400">
                                    The Candlish Glass Works operated from 1853 to 1921, producing millions of bottles, jars, and glass containers. The factory employed hundreds of local workers and was a cornerstone of Seaham's industrial economy during the height of the Victorian industrial revolution.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-3">Industrial Waste Becomes Treasure</h3>
                                <p class="text-slate-600 dark:text-slate-400">
                                    For nearly 70 years, the factory's waste glass and broken products were simply dumped directly into the North Sea. What seemed like industrial pollution at the time has become Seaham's greatest natural treasure, as the sea has spent over a century transforming this waste into beautiful seaglass.
                                </p>
                            </div>

                            <div>
                                <h3 class="text-xl font-semibold text-slate-800 dark:text-slate-200 mb-3">Nature's Artistry</h3>
                                <p class="text-slate-600 dark:text-slate-400">
                                    The North Sea's powerful waves, combined with sand and time, have tumbled these glass fragments for over 100 years. This natural process has created the smooth, frosted gems that now wash up on Seaham's beaches, making it one of the world's premier seaglass collecting destinations.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Section -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-semibold text-slate-800 dark:text-slate-200 mb-8 text-center">Seaham Seaglass Timeline</h3>

                <div class="relative">
                    <!-- Timeline line -->
                    <div class="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-200 dark:bg-blue-700"></div>

                    <div class="space-y-12">
                        <!-- 1853 -->
                        <div class="relative flex items-center">
                            <div class="flex-1 pr-8 text-right">
                                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                    <h4 class="font-semibold text-blue-900 dark:text-blue-200">1853</h4>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Seaham Bottle Works established by the Candlish family</p>
                                </div>
                            </div>
                            <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-blue-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                            <div class="flex-1 pl-8"></div>
                        </div>

                        <!-- 1853-1921 -->
                        <div class="relative flex items-center">
                            <div class="flex-1 pr-8"></div>
                            <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-green-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                            <div class="flex-1 pl-8">
                                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                                    <h4 class="font-semibold text-green-900 dark:text-green-200">1853-1921</h4>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">68 years of glass production and waste disposal into the North Sea</p>
                                </div>
                            </div>
                        </div>

                        <!-- 1921 -->
                        <div class="relative flex items-center">
                            <div class="flex-1 pr-8 text-right">
                                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4">
                                    <h4 class="font-semibold text-amber-900 dark:text-amber-200">1921</h4>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Factory closes, but the sea continues its work on the glass waste</p>
                                </div>
                            </div>
                            <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-amber-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                            <div class="flex-1 pl-8"></div>
                        </div>

                        <!-- Present Day -->
                        <div class="relative flex items-center">
                            <div class="flex-1 pr-8"></div>
                            <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-purple-500 rounded-full border-4 border-white dark:border-gray-800"></div>
                            <div class="flex-1 pl-8">
                                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                                    <h4 class="font-semibold text-purple-900 dark:text-purple-200">Present Day</h4>
                                    <p class="text-slate-600 dark:text-slate-400 text-sm">Over 100 years later, Seaham is world-famous for its abundant, high-quality seaglass</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <!-- Seaglass Information -->
            <div>
                <div class="bg-gradient-to-br from-blue-50 to-green-50 dark:from-blue-900/20 dark:to-green-900/20 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-200 mb-6">What is Seaglass?</h2>

                    <div class="space-y-4 text-slate-600 dark:text-slate-400">
                        <p>
                            Seaglass, also known as beach glass or sea glass, is created when pieces of glass are naturally tumbled by ocean waves and sand over many years. This process creates smooth, frosted gems that wash up on our beautiful Seaham beaches.
                        </p>

                        <p>
                            Seaham is internationally renowned as one of the world's best seaglass collecting locations due to its unique industrial heritage. The Victorian-era Candlish Glass Works (1853-1921) left behind an extraordinary legacy - decades of glass waste that the North Sea has transformed into a collector's paradise.
                        </p>

                        <p>
                            What makes Seaham seaglass special is not just its abundance, but its exceptional quality and rare colors. The industrial origin means you can find unusual colors that are virtually impossible to find elsewhere, including the famous Seaham "multi" pieces that display multiple colors in a single fragment.
                        </p>

                        <p>
                            Each piece tells a story of Victorian industry, maritime power, and nature's remarkable ability to transform human waste into objects of beauty. The glass fragments have been tumbling in the North Sea for over a century, creating the perfect frosted finish that makes Seaham seaglass so highly prized by collectors worldwide.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Collecting Tips -->
            <div>
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-8">
                    <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-200 mb-6">Collecting Tips</h2>

                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-amber-100 dark:bg-amber-900 p-2 rounded-lg mt-1">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 dark:text-slate-200">Best Times</h3>
                                <p class="text-slate-600 dark:text-slate-400">Early morning after high tide or storms when new treasures are revealed.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mt-1">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 dark:text-slate-200">Best Locations</h3>
                                <p class="text-slate-600 dark:text-slate-400">Seaham Hall Beach, Nose's Point, and the areas around the harbor are prime spots.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 dark:bg-green-900 p-2 rounded-lg mt-1">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-slate-800 dark:text-slate-200">What to Look For</h3>
                                <p class="text-slate-600 dark:text-slate-400">Smooth, frosted pieces without sharp edges. Rare colors like red, orange, and cobalt blue are special finds!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colors and Rarity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-12">
            <h2 class="text-2xl font-semibold text-slate-800 dark:text-slate-200 mb-8 text-center">Seaglass Colors & Rarity</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-3 shadow-inner"></div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">White/Clear</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Very Common</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-300 rounded-full mx-auto mb-3 shadow-inner"></div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">Green</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Common</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-400 rounded-full mx-auto mb-3 shadow-inner"></div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">Blue</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Uncommon</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-red-400 rounded-full mx-auto mb-3 shadow-inner"></div>
                    <h3 class="font-semibold text-slate-800 dark:text-slate-200">Red</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400">Very Rare</p>
                </div>
            </div>
        </div>

        <!-- Safety Notice -->
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <div class="bg-amber-100 dark:bg-amber-900 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.168 15.5C3.398 17.333 4.36 19 5.9 19z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-amber-800 dark:text-amber-200 mb-2">Safety</h3>
                    <p class="text-amber-700 dark:text-amber-300">
                        Always be mindful of tides and weather conditions when collecting.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
