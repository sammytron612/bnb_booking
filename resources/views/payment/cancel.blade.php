<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - Eileen's B&B</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <!-- Cancel Icon -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                    <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Payment Cancelled
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Your payment was cancelled. Your booking is still pending payment.
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
                        <dd class="text-sm text-gray-900">{{ $booking->depart->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Check-out:</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->leave->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-medium text-gray-900">Amount Due:</dt>
                        <dd class="text-sm font-bold text-gray-900">£{{ number_format($booking->total_price, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="flex-shrink-0 h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 15c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Booking Status</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Your booking is still reserved but requires payment to be confirmed. You can try paying again or contact us for alternative payment methods.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center space-x-4">
                <button onclick="retryPayment()"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Try Payment Again
                </button>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Return to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        <script>
        function retryPayment() {
            // Redirect to signed payment checkout URL
            window.location.href = '{{ $retryPaymentUrl }}';
        }
    </script>
    </script>
</body>
</html>
