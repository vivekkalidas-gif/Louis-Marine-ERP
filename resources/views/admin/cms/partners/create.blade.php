<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Add New Partner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.cms.partners.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-marine-800 dark:border-marine-700 dark:text-white">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Website -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website URL</label>
                                <input type="url" name="website_url" value="{{ old('website_url') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-marine-800 dark:border-marine-700 dark:text-white">
                                @error('website_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Logo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo</label>
                                <input type="file" name="logo_path" class="mt-1 block w-full text-gray-700 dark:text-gray-300">
                                @error('logo_path') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
                                <input type="number" name="order" value="{{ old('order', 0) }}" class="mt-1 block w-24 rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500 dark:bg-marine-800 dark:border-marine-700 dark:text-white">
                            </div>

                            <!-- Is Active -->
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-marine-600 shadow-sm focus:border-marine-300 focus:ring focus:ring-marine-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-gray-700 dark:text-gray-300">Active</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <a href="{{ route('admin.cms.partners.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 mr-4">Cancel</a>
                                <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                                    Save Partner
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
