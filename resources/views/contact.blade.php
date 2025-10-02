<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-slate-800 dark:text-slate-200 mb-4">Contact Us</h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                We'd love to hear from you! Get in touch with any questions about our beautiful seaside accommodations in Seaham.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="order-2 lg:order-1">
                <livewire:contact-form />
            </div>

            <!-- Contact Information -->
            <div class="order-1 lg:order-2">
                <div class="bg-slate-50 dark:bg-gray-800 rounded-xl p-8">
                    <h3 class="text-2xl font-semibold text-slate-800 dark:text-slate-200 mb-6">Get in Touch</h3>
                    
                    <div class="space-y-6">
                        <!-- Email -->
                        <div class="flex items-start space-x-4">
                            <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 dark:text-slate-200">Email</h4>
                                <p class="text-slate-600 dark:text-slate-400">{{ config('app.owner_email', 'info@eileenbnb.com') }}</p>
                            </div>
                        </div>

                        <!-- Phone (if available) -->
                        @if(config('app.owner_phone'))
                        <div class="flex items-start space-x-4">
                            <div class="bg-green-100 dark:bg-green-900 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 dark:text-slate-200">Phone</h4>
                                <p class="text-slate-600 dark:text-slate-400">{{ config('app.owner_phone') }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Location -->
                        <div class="flex items-start space-x-4">
                            <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 dark:text-slate-200">Location</h4>
                                <p class="text-slate-600 dark:text-slate-400">Seaham, County Durham<br>United Kingdom</p>
                            </div>
                        </div>

                        <!-- Response Time -->
                        <div class="flex items-start space-x-4">
                            <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-lg">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800 dark:text-slate-200">Response Time</h4>
                                <p class="text-slate-600 dark:text-slate-400">We typically respond within 24 hours</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h4 class="font-semibold text-blue-800 dark:text-blue-200 mb-2">Quick Booking</h4>
                        <p class="text-blue-700 dark:text-blue-300 text-sm">
                            Ready to book? Visit our <a href="{{ route('home') }}" class="underline hover:text-blue-600">homepage</a> to check availability and make a reservation instantly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>