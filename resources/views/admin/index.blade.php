<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Admin Dashboard
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Manage your bed & breakfast operations from here
            </p>
        </div>

        <!-- Admin Menu Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

            <!-- Properties Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ \App\Models\Venue::count() }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Properties
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                        Manage venues and property images
                    </p>
                    <a href="{{ route('admin.properties') }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium text-sm">
                        Manage Properties
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Bookings Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4h3a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2h3z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ \App\Models\Booking::count() }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Bookings
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                        View and manage all property bookings
                    </p>
                    <a href="{{ route('admin.bookings') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium text-sm">
                        Manage Bookings
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Reviews Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ \App\Models\Review::count() }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Reviews
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                        Monitor and respond to guest reviews
                    </p>
                    <a href="{{ route('admin.reviews') }}" class="inline-flex items-center text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 font-medium text-sm">
                        View Reviews
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Refunds Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-red-600 dark:text-red-400">
                            {{ \App\Models\Booking::where('status', 'refunded')->count() }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Refunds
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                        Process customer refunds and cancellations
                    </p>
                    <a href="{{ route('admin.refunds') }}" class="inline-flex items-center text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 font-medium text-sm">
                        Manage Refunds
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Analytics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                            £{{ number_format(\App\Models\Booking::where('is_paid', true)->sum('total_price'), 0) }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Analytics
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                        View revenue and performance metrics
                    </p>
                    <a href="{{ route('admin.analytics') }}" class="inline-flex items-center text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-medium text-sm">
                        View Analytics
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ \App\Models\User::count() }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Settings
                    </h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                        Manage system settings and preferences
                    </p>
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium text-sm">
                        System Settings
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

        </div>

        <!-- Quick Stats Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Quick Overview
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Recent Bookings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Recent Bookings
                    </h3>
                    <div class="space-y-3">
                        @php
                            $recentBookings = \App\Models\Booking::latest()->limit(3)->get();
                        @endphp
                        @forelse($recentBookings as $booking)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $booking->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $booking->venue->venue_name }}</p>
                                </div>
                                <span class="text-sm font-medium text-green-600 dark:text-green-400">
                                    £{{ number_format($booking->total_price, 0) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No recent bookings</p>
                        @endforelse
                    </div>
                </div>

                <!-- Revenue This Month -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        This Month
                    </h3>
                    <div class="space-y-3">
                        @php
                            $thisMonthBookings = \App\Models\Booking::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)->count();
                            $thisMonthRevenue = \App\Models\Booking::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->where('is_paid', true)->sum('total_price');
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Bookings</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">{{ $thisMonthBookings }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Revenue</span>
                            <span class="font-bold text-green-600 dark:text-green-400">£{{ number_format($thisMonthRevenue, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Avg. Rating</span>
                            <span class="font-bold text-yellow-600 dark:text-yellow-400">
                                {{ number_format(\App\Models\Review::avg('rating'), 1) }}/5
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Property Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Property Status
                    </h3>
                    <div class="space-y-3">
                        @php
                            $lightHouseVenue = \App\Models\Venue::where('venue_name', 'LIKE', '%light%house%')->first();
                            $sarasVenue = \App\Models\Venue::where('venue_name', 'LIKE', '%saras%')->first();
                            $lightHouseBookings = $lightHouseVenue ? \App\Models\Booking::where('venue_id', $lightHouseVenue->id)
                                ->where('check_in', '<=', now())
                                ->where('check_out', '>=', now())->count() : 0;
                            $sarasBookings = $sarasVenue ? \App\Models\Booking::where('venue_id', $sarasVenue->id)
                                ->where('check_in', '<=', now())
                                ->where('check_out', '>=', now())->count() : 0;
                        @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Light House</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $lightHouseBookings > 0 ? 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400' : 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400' }}">
                                {{ $lightHouseBookings > 0 ? 'Occupied' : 'Available' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-300">Saras</span>
                            <span class="px-2 py-1 text-xs rounded-full {{ $sarasBookings > 0 ? 'bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-400' : 'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-400' }}">
                                {{ $sarasBookings > 0 ? 'Occupied' : 'Available' }}
                            </span>
                        </div>
                        <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-300">Total Reviews</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ \App\Models\Review::count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
