<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Payment Successful!
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Your booking with {{ config('app.name') }} has been confirmed and payment processed successfully.
                </p>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Details</h3>

                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Booking Reference:</dt>
                        <dd class="text-sm text-gray-900 font-mono">{{ $booking->getDisplayBookingId() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Guest Name:</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Property:</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->venue->venue_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Check-in:</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->check_in->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Check-out:</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->check_out->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Nights:</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->nights }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-medium text-gray-900">Total Paid:</dt>
                        <dd class="text-sm font-bold text-gray-900">£{{ number_format($booking->total_price, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">What's Next?</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>A confirmation email has been sent to {{ $booking->email }}. We'll contact you soon with check-in details and any additional information.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Return to Home
                </a>
                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Print Confirmation
                </button>
            </div>
        </div>
    </div>
</body>
</html>
