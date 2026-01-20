<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit CMS Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <form action="{{ route('admin.cms.pages.update', $page) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Title</label>
                            <input type="text" name="title" id="title" value="{{ $page->title }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="slug" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Slug</label>
                            <input type="text" name="slug" id="slug" value="{{ $page->slug }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="meta_title" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ $page->meta_title }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="meta_description" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" rows="3">{{ $page->meta_description }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="hidden" name="is_published" value="0">
                                <input type="checkbox" name="is_published" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ $page->is_published ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300">Publish Page</span>
                            </label>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Page
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
