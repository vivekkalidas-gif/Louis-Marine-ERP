<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manage Sections: ') }} {{ $page->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="gap-6 grid grid-cols-1 lg:grid-cols-3">
                
                <!-- Add New Section Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Add New Section</h3>
                            <form action="{{ route('admin.cms.pages.sections.store', $page) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="section_key" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Section Key (Unique)</label>
                                    <input type="text" name="section_key" id="section_key" placeholder="e.g., hero, vision, mission" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" required>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Used to identify this section in the template.</p>
                                </div>
                                <div class="mb-4">
                                    <label for="title" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Title</label>
                                    <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label for="content" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Content</label>
                                    <textarea name="content" id="content" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline" rows="4"></textarea>
                                </div>
                                <div class="mb-4">
                                    <label for="image" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Image (Optional)</label>
                                    <input type="file" name="image" id="image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label for="order" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Order</label>
                                    <input type="number" name="order" id="order" value="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
                                </div>
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_visible" value="1" checked class="form-checkbox h-5 w-5 text-blue-600">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Visible</span>
                                    </label>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Add Section
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Existing Sections List -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Existing Sections</h3>
                            
                            @if($sections->count() > 0)
                                <div class="space-y-4">
                                    @foreach($sections as $section)
                                        <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                                            <form action="{{ route('admin.cms.sections.update', $section) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 dark:text-gray-300 mr-2 mb-2">#{{ $section->section_key }}</span>
                                                        <input type="text" name="title" value="{{ $section->title }}" class="font-bold text-lg bg-transparent border-b border-gray-300 focus:border-blue-500 focus:outline-none w-full mb-2" placeholder="Section Title">
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="number" name="order" value="{{ $section->order }}" class="w-16 p-1 border rounded mr-2 text-center" title="Order">
                                                        <form action="{{ route('admin.cms.sections.destroy', $section) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this section?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                                        </form>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <textarea name="content" rows="3" class="w-full p-2 border rounded text-sm mb-2" placeholder="Content">{{ $section->content }}</textarea>
                                                        <div class="flex items-center mt-2">
                                                            <input type="checkbox" name="is_visible" value="1" {{ $section->is_visible ? 'checked' : '' }} class="mr-2">
                                                            <span class="text-sm text-gray-600">Visible</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @if($section->image_path)
                                                            <img src="{{ asset('storage/' . $section->image_path) }}" class="h-20 w-auto object-cover rounded mb-2">
                                                        @endif
                                                        <input type="file" name="image" class="text-sm">
                                                    </div>
                                                </div>
                                                <div class="mt-2 text-right">
                                                    <button type="submit" class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 py-1 px-3 rounded">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 italic">No sections added yet.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
