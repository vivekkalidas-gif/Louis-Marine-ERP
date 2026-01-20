<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Edit CMS Page') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.cms-pages.update', $cmsPage->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Title</label>
                            <input type="text" name="title" value="{{ $cmsPage->title }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Slug</label>
                            <input type="text" value="{{ $cmsPage->slug }}" class="w-full p-2 border rounded dark:bg-marine-800 dark:border-marine-700 bg-gray-100 dark:text-gray-400" disabled>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-bold mb-2">Content</label>
                            <textarea name="content" class="w-full p-2 border rounded h-64 dark:bg-marine-800 dark:border-marine-700" required>{{ $cmsPage->content }}</textarea>
                        </div>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_published" class="mr-2" {{ $cmsPage->is_published ? 'checked' : '' }}>
                                <span class="text-sm">Published</span>
                            </label>
                        </div>
                        <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                            Update Page
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
