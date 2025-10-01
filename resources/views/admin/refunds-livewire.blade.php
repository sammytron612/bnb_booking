<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Refund Management
            </h1>
            <p class="text-gray-600 dark:text-gray-300">
                Process and manage booking refunds
            </p>
        </div>

        <!-- Back to Dashboard -->
        <div class="mb-6">
            <a href="{{ route('admin.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>

        <!-- Refunds Table Component -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <livewire:admin.refunds-table />
        </div>
    </div>
</x-layouts.app>
