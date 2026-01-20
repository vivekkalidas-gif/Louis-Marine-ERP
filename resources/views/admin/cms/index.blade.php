<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('CMS Pages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.cms-pages.create') }}" class="bg-marine-600 hover:bg-marine-700 text-white font-bold py-2 px-4 rounded">
                    Create New Page
                </a>
            </div>
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="p-2 border-b dark:border-marine-800">Title</th>
                                <th class="p-2 border-b dark:border-marine-800">Slug</th>
                                <th class="p-2 border-b dark:border-marine-800">Status</th>
                                <th class="p-2 border-b dark:border-marine-800">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pages as $page)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800">
                                <td class="p-2 border-b dark:border-marine-800">{{ $page->title }}</td>
                                <td class="p-2 border-b dark:border-marine-800">{{ $page->slug }}</td>
                                <td class="p-2 border-b dark:border-marine-800">
                                    <span class="px-2 py-1 text-xs rounded {{ $page->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $page->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="p-2 border-b dark:border-marine-800">
                                    <a href="{{ route('admin.cms-pages.edit', $page->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 mr-2">Edit</a>
                                    <form action="{{ route('admin.cms-pages.destroy', $page->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-500 dark:hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
