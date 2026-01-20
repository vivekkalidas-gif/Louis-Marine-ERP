<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Hero Banner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.cms.landing-page.hero-slides.update', $heroSlide) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2">Title *</label>
                            <input type="text" name="title" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('title', $heroSlide->title) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Subtitle</label>
                            <input type="text" name="subtitle" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('subtitle', $heroSlide->subtitle) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">{{ old('description', $heroSlide->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Background Image</label>
                            @if($heroSlide->background_image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($heroSlide->background_image) }}" alt="Current" class="h-32 rounded">
                                </div>
                            @endif
                            <input type="file" name="background_image" accept="image/*" class="w-full">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Background Video URL</label>
                            <input type="url" name="background_video_url" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('background_video_url', $heroSlide->background_video_url) }}">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">CTA Button 1 Text</label>
                                <input type="text" name="cta_text_1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('cta_text_1', $heroSlide->cta_text_1) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">CTA Button 1 Link</label>
                                <input type="text" name="cta_link_1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('cta_link_1', $heroSlide->cta_link_1) }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">CTA Button 2 Text</label>
                                <input type="text" name="cta_text_2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('cta_text_2', $heroSlide->cta_text_2) }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">CTA Button 2 Link</label>
                                <input type="text" name="cta_link_2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500" value="{{ old('cta_link_2', $heroSlide->cta_link_2) }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Sort Order</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', $heroSlide->sort_order) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $heroSlide->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-marine-600 shadow-sm focus:border-marine-500 focus:ring-marine-500">
                                <label class="ml-2 text-sm font-medium">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-4">
                        <button type="submit" class="bg-marine-600 hover:bg-marine-700 text-white px-6 py-2 rounded-lg transition">
                            Update Slide
                        </button>
                        <a href="{{ route('admin.cms.landing-page.hero-slides.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
