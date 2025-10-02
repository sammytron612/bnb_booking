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



        <!-- Refunds Table Component -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg">
            <livewire:admin.refunds-table />
        </div>
    </div>
</x-layouts.app>
