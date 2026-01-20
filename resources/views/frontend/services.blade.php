<x-layouts.frontend title="Services | Louis Marine Qatar">
    <!-- Page Header -->
    <div class="bg-[#0b1c3c] py-20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg')] bg-cover bg-center"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl md:text-6xl">Our Services</h1>
            <p class="mt-4 text-xl text-gray-300 max-w-3xl mx-auto font-light">
                Comprehensive marine engineering, safety inspections, and maintenance solutions compliant with international standards.
            </p>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="py-16 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-100 p-8 rounded-xl shadow-md hover:shadow-2xl transition duration-300 group border-b-4 border-transparent hover:border-red-600">
                        <div class="h-16 w-16 bg-blue-50 rounded-full flex items-center justify-center mb-6 group-hover:bg-red-50 transition">
                            {!! $service->icon_class !!}
                        </div>
                        <h3 class="text-xl font-bold text-[#0b1c3c] mb-4 group-hover:text-red-600 transition">{{ $service->title }}</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            {{ $service->description }}
                        </p>
                        <a href="{{ route('contact', ['service' => $service->title]) }}" class="text-sm font-bold text-red-600 hover:text-[#0b1c3c] transition inline-flex items-center">
                            REQUEST SERVICE <span class="ml-2">&rarr;</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-extrabold text-[#0b1c3c]">Need a Custom Solution?</h2>
            <p class="mt-4 text-lg text-gray-600">Contact our engineering team to discuss your specific vessel requirements.</p>
            <div class="mt-8">
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-bold rounded-full text-white bg-red-600 hover:bg-red-700 md:text-lg shadow-lg transition">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
</x-layouts.frontend>
