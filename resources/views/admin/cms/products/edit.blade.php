<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Website Product') }} - {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.cms.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Status --}}
                        <div class="md:col-span-2">
                             <label class="inline-flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ $product->is_active ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700 dark:text-gray-300 font-bold">Product is Published / Visible</span>
                            </label>
                        </div>

                        {{-- Basic Info --}}
                        <div class="md:col-span-2">
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Product Name</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white mt-1" required>
                        </div>

                        <div class="md:col-span-2">
                             <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Short Description</label>
                             <textarea name="short_description" rows="2" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white mt-1" required>{{ old('short_description', $product->short_description) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                             <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Full Description</label>
                             <textarea name="description" rows="5" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white mt-1">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">Main Image</label>
                            @if($product->image_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($product->image_path) }}" class="h-20 rounded">
                                </div>
                            @endif
                            <input type="file" name="image_path" class="w-full mt-1 text-gray-500 dark:text-gray-400">
                        </div>

                        {{-- SEO --}}
                        <div class="md:col-span-2 border-t pt-4 mt-4">
                            <h3 class="font-bold mb-2 dark:text-white">SEO Meta Data</h3>
                        </div>
                        
                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">SEO Title</label>
                            <input type="text" name="seo_title" value="{{ old('seo_title', $product->seo_title) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white mt-1">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700 dark:text-gray-300">SEO Description</label>
                            <input type="text" name="seo_description" value="{{ old('seo_description', $product->seo_description) }}" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white mt-1">
                        </div>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-bold">Update Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
