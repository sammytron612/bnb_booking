<x-layouts.app>
    <div class="min-h-screen bg-gray-100">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Submit Your Review</h1>
                                <p class="mt-2 text-gray-600">We value your feedback!</p>
                            </div>
                            <a href="{{ route('home') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Home
                            </a>
                        </div>
                    </div>

                    <div class="p-6 sm:px-20 bg-gray-200 bg-opacity-25">
                        @livewire('customer-review-form', ['booking' => $booking])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

