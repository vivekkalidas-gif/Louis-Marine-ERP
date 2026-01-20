<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Import Categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-alert-message />
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('storekeeper.categories.import.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload CSV File</label>
                            <input type="file" name="file" id="file" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                CSV Format: Name, Description
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Import') }}</x-primary-button>
                            <a href="{{ route('storekeeper.categories.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
