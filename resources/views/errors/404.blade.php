<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title>Page Not Found - Seaham Coastal Retreats</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8 text-center">
            <div class="text-6xl font-bold text-gray-300 mb-4">404</div>
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Page Not Found</h1>
            <p class="text-gray-600 mb-6">
                The page you're looking for doesn't exist or has been moved.
            </p>
            <a href="{{ route('home') }}"
               class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Return Home
            </a>
        </div>
    </div>
</body>
</html>
