<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Create CMS Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.cms-pages.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Title</label>
                            <input type="text" name="title" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Content</label>
                            <textarea name="content" class="w-full p-2 border rounded h-64 dark:bg-marine-800 dark:border-marine-700" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_published" class="mr-2">
                                <span class="text-sm">Publish Immediately</span>
                            </label>
                        </div>
                        <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                            Create Page
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
