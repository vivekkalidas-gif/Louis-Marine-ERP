<x-layouts.frontend title="About Us | Louis Marine Qatar">
    <!-- Page Header -->
    <div class="bg-[#0b1c3c] py-20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg')] bg-cover bg-center"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">{{ $page->title }}</h1>
            <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto font-light">
                {{ $page->meta_description }}
            </p>
        </div>
    </div>

    <!-- Dynamic Sections -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-20">
            @foreach($page->sections as $section)
                <div class="flex flex-col md:flex-row gap-12 items-center {{ $loop->even ? 'md:flex-row-reverse' : '' }}">
                    <div class="w-full md:w-1/2">
                        @if($section->image_path)
                             <!-- Handle both local storage and external URLs -->
                             <img src="{{ Str::startsWith($section->image_path, 'http') ? $section->image_path : url('storage/' . $section->image_path) }}" 
                                  alt="{{ $section->title }}" 
                                  class="rounded-lg shadow-2xl object-cover w-full h-[400px]">
                        @endif
                    </div>
                    <div class="w-full md:w-1/2">
                        @if($section->title)
                            <h4 class="text-red-600 font-bold uppercase tracking-widest mb-2">{{ $section->subtitle ?? 'About Us' }}</h4>
                            <h2 class="text-3xl font-extrabold text-[#0b1c3c] mb-6">{{ $section->title }}</h2>
                        @endif
                        <div class="prose prose-lg text-gray-600 leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($section->content)) !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.frontend>
