<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-800 dark:text-slate-200 mb-4">Seaglass</h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                Discover the natural treasure of Seaham's coastline - beautiful seaglass washed up by the North Sea, creating unique gems perfect for collectors and beach enthusiasts.
            </p>
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
                            Seaham is particularly famous for its seaglass due to its industrial heritage. The Victorian glass works that once operated here left behind a legacy of colorful glass fragments that continue to be transformed by the North Sea into stunning collectible pieces.
                        </p>

                        <p>
                            Each piece is unique, shaped by time and tide into a one-of-a-kind treasure. Colors range from common whites and greens to rare blues, purples, and even the coveted red seaglass.
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
                    <h3 class="font-semibold text-amber-800 dark:text-amber-200 mb-2">Safety & Conservation</h3>
                    <p class="text-amber-700 dark:text-amber-300">
                        Always be mindful of tides and weather conditions when collecting. Take only what you need and leave plenty for other collectors to enjoy. Be aware of cliff stability and never turn your back on the sea.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
