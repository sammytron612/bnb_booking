<x-layouts.app>
<div class="min-h-screen bg-gray-100">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Reviews Management</h1>
                            <p class="mt-2 text-gray-600">Manage customer reviews and replies</p>
                        </div>
                        <a href="{{ route('admin.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Dashboard
                        </a>
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25">
                    @livewire('reviews-table')
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
