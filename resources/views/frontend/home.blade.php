<x-layouts.frontend>
    <!-- Hero Section -->
    <!-- Hero Section -->
    <div class="relative bg-marine-900 overflow-hidden h-screen max-h-[800px] flex items-center">
        <!-- Full Width Background Image -->
        <div class="absolute inset-0 z-0">
             @if($hero && $hero->background_image)
                <div class="w-full h-full bg-cover bg-center" style="background-image: url('{{ url('storage/' . $hero->background_image) }}');"></div>
             @else
                <div class="w-full h-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1566415715202-b25868cc932a?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');"></div>
             @endif
             <!-- Subtle Overlay to ensure text readability -->
             <div class="absolute inset-0 bg-black/30"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="sm:text-center lg:text-left">
                <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl drop-shadow-lg">
                    @if($hero)
                        {!! nl2br(e($hero->title)) !!}
                    @else
                        <span class="block xl:inline">Premium Marine</span>
                        <span class="block text-marine-400 xl:inline">Services in Qatar</span>
                    @endif
                </h1>
                <p class="mt-3 text-base text-gray-100 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0 drop-shadow-md font-medium">
                    {{ $hero ? $hero->description : 'Providing world-class marine engineering, surveying, and maintenance solutions tailored to your needs.' }}
                </p>
                <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start gap-4">
                    @if($hero && $hero->cta_text_1)
                        <div class="rounded-md shadow">
                            <a href="{{ $hero->cta_link_1 ?? route('contact') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-marine-600 hover:bg-marine-700 md:py-4 md:text-lg md:px-10">
                                {{ $hero->cta_text_1 }}
                            </a>
                        </div>
                    @else
                        <div class="rounded-md shadow">
                            <a href="{{ route('contact') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-marine-600 hover:bg-marine-700 md:py-4 md:text-lg md:px-10">
                                Get Started
                            </a>
                        </div>
                    @endif
                    
                    @if($hero && $hero->cta_text_2)
                        <div class="">
                            <a href="{{ $hero->cta_link_2 ?? route('page', 'services') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-marine-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                                {{ $hero->cta_text_2 }}
                            </a>
                        </div>
                    @else
                        <div class="">
                            <a href="{{ route('page', 'services') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-marine-700 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                                Our Services
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="bg-marine-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
                @foreach($stats as $stat)
                    <div class="text-center">
                        <div class="text-4xl font-extrabold text-marine-200">{{ $stat->value }}</div>
                        <div class="mt-2 text-sm font-medium text-gray-300 uppercase tracking-wider">{{ $stat->label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Services Section -->
    <div class="py-12 bg-white dark:bg-gray-900" id="services">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center">
                <h2 class="text-base text-marine-600 font-semibold tracking-wide uppercase">Services</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Comprehensive Marine Solutions
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 lg:mx-auto">
                    We offer a wide range of specialized services to ensure the safety and efficiency of your marine assets.
                </p>
            </div>

            <div class="mt-10">
                <dl class="space-y-10 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-x-8 md:gap-y-10">
                    @foreach($features as $feature)
                        <div class="relative">
                            <dt>
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-marine-500 text-white">
                                    @if($feature->icon)
                                         {!! $feature->icon !!}
                                    @else
                                        <!-- Default Icon -->
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="ml-16 text-lg leading-6 font-medium text-gray-900 dark:text-white">{{ $feature->title }}</p>
                            </dt>
                            <dd class="mt-2 ml-16 text-base text-gray-500 dark:text-gray-400">
                                {{ $feature->description }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </div>

    <!-- Certifications Section -->
    @if($certifications->count() > 0)
    <div class="py-12 bg-gray-50 dark:bg-marine-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-10">
                <h2 class="text-base text-marine-600 font-semibold tracking-wide uppercase">Accreditations</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Certified & Compliant
                </p>
            </div>
            <div class="flex flex-wrap justify-center gap-8 items-center">
                @foreach($certifications as $cert)
                    <div class="flex flex-col items-center p-4 bg-white dark:bg-marine-900 rounded-lg shadow-sm w-48 text-center">
                        @if($cert->image_path)
                            <img src="{{ url('storage/' . $cert->image_path) }}" class="h-24 w-auto object-contain mb-4" alt="{{ $cert->name }}">
                        @else
                             <div class="h-24 w-24 bg-gray-200 rounded-full flex items-center justify-center mb-4 text-gray-400">
                                <span class="text-xs">No Image</span>
                             </div>
                        @endif
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $cert->name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $cert->issuing_body }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Testimonials Section -->
    @if($testimonials->count() > 0)
    <div class="py-12 bg-white dark:bg-marine-900 border-t border-gray-100 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-10">
                <h2 class="text-base text-marine-600 font-semibold tracking-wide uppercase">Testimonials</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    What Our Clients Say
                </p>
            </div>
            <div class="grid gap-8 lg:grid-cols-3">
                @foreach($testimonials as $testimonial)
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-8 shadow-sm hover:shadow-md transition">
                        <div class="flex items-center mb-4">
                            @if($testimonial->client_photo)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $testimonial->client_photo }}" alt="{{ $testimonial->client_name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-marine-100 flex items-center justify-center text-marine-600 font-bold">
                                    {{ substr($testimonial->client_name, 0, 1) }}
                                </div>
                            @endif
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $testimonial->client_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $testimonial->company_name }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 italic">"{{ $testimonial->testimonial_text }}"</p>
                         <div class="mt-4 flex text-yellow-400 text-sm">
                             @for($i=0; $i<$testimonial->rating; $i++)
                                ★
                             @endfor
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Partners Section -->
    @if($partners->count() > 0)
    <div class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-10">
                <h2 class="text-base text-marine-600 font-semibold tracking-wide uppercase">Our Clients</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    Trusted by Industry Leaders
                </p>
            </div>
            <div class="grid grid-cols-2 gap-8 md:grid-cols-4 lg:grid-cols-5">
                @foreach($partners as $partner)
                    <div class="col-span-1 flex justify-center md:col-span-2 lg:col-span-1 transition duration-300">
                         @if($partner->logo_path)
                            <img class="h-12" src="{{ url('storage/' . $partner->logo_path) }}" alt="{{ $partner->name }}">
                         @else
                            <span class="text-xl font-bold text-gray-400">{{ $partner->name }}</span>
                         @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- CTA Sections -->
    @foreach($ctaSections as $cta)
    <div class="" style="background-color: {{ $cta->background_color ?? '#0b1c3c' }};">
        <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                {{ $cta->title }}
            </h2>
            <p class="mt-4 text-lg leading-6 text-marine-200 text-white/90">
                {{ $cta->description }}
            </p>
            <a href="{{ $cta->button_link ?? route('contact') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-marine-600 bg-white dark:bg-gray-800 hover:bg-marine-50 sm:w-auto">
                {{ $cta->button_text ?? 'Contact Us' }}
            </a>
        </div>
    </div>
    @endforeach
</x-layouts.frontend>
