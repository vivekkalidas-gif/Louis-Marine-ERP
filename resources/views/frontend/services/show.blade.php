<x-layouts.frontend :title="$service->title . ' | Louis Marine Qatar'">
    <!-- Page Header -->
    <div class="bg-[#0b1c3c] py-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg')] bg-cover bg-center"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">{{ $service->title }}</h1>
            <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto font-light">
                {{ $service->description }}
            </p>
        </div>
    </div>

    <!-- Service Content -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    @if($service->image_path)
                        <img src="{{ url('storage/' . $service->image_path) }}" alt="{{ $service->title }}" class="w-full h-auto rounded-xl shadow-lg mb-8 object-cover max-h-[500px]">
                    @endif

                    <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                        @if($service->content)
                            {!! nl2br(e($service->content)) !!}
                        @else
                            <p class="text-gray-500 dark:text-gray-400 italic">Detailed information for this service is coming soon.</p>
                            <p>{{ $service->description }}</p>
                        @endif
                    </div>

                    <div class="mt-12 bg-gray-50 dark:bg-gray-700 p-8 rounded-xl border-l-4 border-red-600">
                        <h3 class="text-xl font-bold text-[#0b1c3c] mb-4">Request this Service</h3>
                        <p class="text-gray-600 mb-6">Interested in {{ $service->title }}? Keep your vessels safe with our certified solutions.</p>
                        <a href="{{ route('contact', ['service' => $service->title]) }}" class="inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded shadow-lg transition transform hover:-translate-y-1">
                            Get a Quote
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Other Services -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-[#0b1c3c] mb-4 border-b-2 border-red-200 pb-2">Our Services</h3>
                        <ul class="space-y-3">
                            @foreach(\App\Models\CmsService::where('is_active', true)->where('id', '!=', $service->id)->orderBy('order')->get() as $otherService)
                                <li>
                                    <a href="{{ route('services.show', $otherService) }}" class="flex items-center text-gray-600 hover:text-red-600 transition group">
                                        <span class="w-2 h-2 bg-red-400 rounded-full mr-3 group-hover:bg-red-600"></span>
                                        {{ $otherService->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Contact Card -->
                    <div class="bg-[#0b1c3c] text-white p-6 rounded-xl shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-red-600 rounded-full opacity-20"></div>
                        <h3 class="text-lg font-bold mb-4">Need Assistance?</h3>
                        <p class="text-gray-300 text-sm mb-6">Contact our support team 24/7 for emergency requirements.</p>
                        <a href="tel:+97444603388" class="flex items-center space-x-3 text-xl font-bold hover:text-red-400 transition">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>+974 4460 3388</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.frontend>
