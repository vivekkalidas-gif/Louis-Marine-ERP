<x-layouts.frontend title="Products | Louis Marine Qatar">
    <!-- Page Header -->
    <div class="bg-[#0b1c3c] py-20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg')] bg-cover bg-center"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">{{ $page->title ?? 'Our Products' }}</h1>
            <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto font-light">
                {{ $page->meta_description ?? 'High-quality marine safety and fire fighting equipment.' }}
            </p>
        </div>
    </div>

    <!-- Dynamic Sections -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">
            @if($page && $page->sections->count() > 0)
                @foreach($page->sections as $section)
                    <div class="flex flex-col md:flex-row gap-12 items-center {{ $loop->even ? 'md:flex-row-reverse' : '' }}">
                        <div class="w-full md:w-1/2">
                            @if($section->image_path)
                                <img src="{{ $section->image_path }}" alt="{{ $section->title }}" class="rounded-lg shadow-2xl object-cover w-full h-[400px]">
                            @endif
                        </div>
                        <div class="w-full md:w-1/2">
                            @if($section->title)
                                <h4 class="text-red-600 font-bold uppercase tracking-widest mb-2">{{ $section->subtitle ?? 'Product Category' }}</h4>
                                <h2 class="text-3xl font-extrabold text-[#0b1c3c] mb-6">{{ $section->title }}</h2>
                            @endif
                            <div class="prose prose-lg text-gray-600 leading-relaxed whitespace-pre-line">
                                {!! $section->content !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-20">
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Product details coming soon.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.frontend>
