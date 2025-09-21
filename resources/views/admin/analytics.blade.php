<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Track your property performance and booking insights</p>
            </div>
            <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">£{{ number_format($totalRevenue, 0) }}</p>
                        <p class="text-sm {{ $revenueGrowth >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% from last month
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">£</span>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Bookings</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalBookings }}</p>
                        <p class="text-sm {{ $bookingGrowth >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }} mt-1">
                            {{ $bookingGrowth >= 0 ? '+' : '' }}{{ $bookingGrowth }}% from last month
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Occupancy Rate -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Occupancy Rate</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $occupancyRate }}%</p>
                        <p class="text-sm text-purple-600 dark:text-purple-400 mt-1">This month</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Average Rating</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($averageRating, 1) }}</p>
                        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-1">From {{ $totalReviews }} reviews</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Revenue Trend</h3>
                    <select class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option>Last 7 days</option>
                        <option>Last 30 days</option>
                        <option>Last 90 days</option>
                    </select>
                </div>
                <div class="h-64 bg-gray-50 dark:bg-gray-700/50 rounded-lg flex items-center justify-center">
                    <p class="text-gray-500 dark:text-gray-400">Revenue chart would be displayed here</p>
                </div>
            </div>

            <!-- Booking Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Booking Distribution by Property</h3>
                <div class="space-y-4">
                    @foreach($venueDistribution as $venue)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $venue['name'] }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $loop->first ? 'bg-blue-600' : 'bg-green-600' }}" style="width: {{ $venue['percentage'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900 dark:text-white font-medium">{{ $venue['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                    @if($venueDistribution->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No booking data available</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity & Top Performing Properties -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Recent Activity</h3>
                <div class="space-y-4">
                    @foreach($recentBookings->take(3) as $booking)
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">New booking confirmed for {{ $booking->venue->venue_name ?? 'Property' }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                    @foreach($recentReviews->take(2) as $review)
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">New {{ $review->rating }}-star review received</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                    @if($recentBookings->isEmpty() && $recentReviews->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No recent activity</p>
                    @endif
                </div>
            </div>

            <!-- Top Performing Properties -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Top Performing Properties</h3>
                <div class="space-y-4">
                    @foreach($venues->take(2) as $venue)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-r {{ $loop->first ? 'from-blue-500 to-blue-600' : 'from-green-500 to-green-600' }} rounded-lg flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm">{{ strtoupper(substr($venue['name'], 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $venue['name'] }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $venue['bookings_count'] }} bookings</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900 dark:text-white">£{{ number_format($venue['revenue'], 0) }}</p>
                                <p class="text-sm text-green-600 dark:text-green-400">
                                    @if($venue['revenue'] > 0)
                                        {{ $venue['bookings_count'] > 0 ? 'Active' : 'No bookings' }}
                                    @else
                                        No revenue
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                    @if($venues->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">No property data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
