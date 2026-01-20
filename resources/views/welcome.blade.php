<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Louis Marine Qatar | Experts in Marine Safety</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-700 text-slate-800">

    <!-- Top Bar -->
    <div class="bg-[#0b1c3c] text-white py-2 text-xs md:text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
            <div class="flex items-center space-x-6">
                <span class="flex items-center space-x-2">
                    <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span>{{ $globalSettings['company_phone'] ?? '+974 4460 3388' }}</span>
                </span>
                <span class="hidden md:flex items-center space-x-2">
                    <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ $globalSettings['company_email'] ?? 'info@louismarineqatar.com' }}</span>
                </span>
            </div>
            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold hover:text-red-400 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold hover:text-red-400 transition">Log in</a>
                    @endauth
                @endif
                <span class="text-gray-400">|</span>
                <a href="#" class="hover:text-red-400 transition">Careers</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <img class="h-16 w-auto" src="https://louismarineqatar.com/wp-content/uploads/2024/11/logo.jpg" alt="Louis Marine Qatar">
                </div>
                
                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8 items-center font-bold text-[#0b1c3c]">
                    <a href="{{ route('home') }}" class="text-red-600">HOME</a>
                    <a href="#about" class="hover:text-red-600 transition">ABOUT US</a>
                    <a href="#services" class="hover:text-red-600 transition">SERVICES</a>
                    <a href="{{ route('products.index') }}" class="hover:text-red-600 transition">PRODUCTS</a>
                    <a href="{{ route('contact') }}" class="hover:text-red-600 transition">CONTACT US</a>
                </nav>

                <!-- Get Quote Button -->
                <div class="hidden md:block">
                    <a href="{{ route('contact') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full transition shadow-lg">
                        GET A QUOTE
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button class="text-gray-800 dark:text-gray-200 hover:text-red-600 focus:outline-none">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative h-[600px] bg-cover bg-center" style="background-image: url('{{ $hero ? $hero->image_path : 'https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg' }}');">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-[#0b1c3c]/60"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
            <div class="text-white max-w-2xl px-4 md:px-0 text-center md:text-left">
                <span class="inline-block bg-red-600 text-white text-xs font-bold px-2 py-1 mb-4 rounded">{{ $hero->content ?? 'EST. 1999' }}</span>
                <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight drop-shadow-md">
                    {!! $hero->title ?? 'Experts in <br> <span class="text-red-500">Marine Safety</span> <br> Equipments' !!}
                </h1>
                <p class="text-lg md:text-xl text-gray-200 mb-8 font-light max-w-lg">
                    {{ $hero->subtitle ?? 'Approved by major classification societies. Protecting life and property at sea for over 25 years.' }}
                </p>
                <div class="flex flex-col md:flex-row gap-4 justify-center md:justify-start">
                    <a href="#services" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded transition shadow-lg text-center">
                        Our Services
                    </a>
                    <a href="#about" class="bg-transparent border-2 border-white hover:bg-white dark:bg-gray-800 hover:text-[#0b1c3c] text-white font-bold py-3 px-8 rounded transition text-center">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="order-2 lg:order-1">
                    <h4 class="text-red-600 font-bold uppercase tracking-widest mb-2">{{ $about->subtitle ?? 'Who We Are' }}</h4>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#0b1c3c] mb-6">{{ $about->title ?? 'Leading Stockist for Marine Safety & Fire Fighting' }}</h2>
                    <div class="text-gray-600 mb-6 leading-relaxed whitespace-pre-line">
                        {!! $about->content ?? 'Louis Marine Equipment Sale...' !!}
                    </div>
                    
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-red-500 mr-2 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">ISO 9001, 14001 & 45001 Certified</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-red-500 mr-2 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">Approved by Class Societies (Lloyds, ABS, DNV)</span>
                        </li>
                    </ul>

                    <a href="#" class="text-[#0b1c3c] font-bold border-b-2 border-red-500 hover:text-red-600 transition">READ OUR STORY &rarr;</a>
                </div>

                <!-- Right Image -->
                <div class="order-1 lg:order-2 relative">
                    <img src="{{ $about->image_path ?? 'https://louismarineqatar.com/wp-content/uploads/2024/11/hm-abut.jpg' }}" alt="About Louis Marine" class="rounded-lg shadow-2xl w-full object-cover h-[500px]">
                    <div class="absolute -bottom-6 -left-6 bg-red-600 p-8 rounded-lg shadow-xl hidden lg:block">
                        <p class="text-5xl font-extrabold text-white text-center">25+</p>
                        <p class="text-white font-medium uppercase tracking-wide text-sm text-center mt-1">Years of<br>Excellence</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50 dark:bg-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h4 class="text-red-600 font-bold uppercase tracking-widest mb-2">What We Do</h4>
                <h2 class="text-3xl md:text-4xl font-extrabold text-[#0b1c3c]">Our Core Activities</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($services as $service)
                <!-- Dynamic Service Item -->
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-md hover:shadow-2xl transition duration-300 group border-b-4 border-transparent hover:border-red-600">
                    <div class="h-16 w-16 bg-blue-50 rounded-full flex items-center justify-center mb-6 group-hover:bg-red-50 transition">
                        {!! $service->icon_class !!}
                    </div>
                    <h3 class="text-xl font-bold text-[#0b1c3c] mb-4 group-hover:text-red-600 transition">{{ $service->title }}</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ $service->description }}
                    </p>
                    <a href="{{ route('services.show', $service) }}" class="text-sm font-bold text-red-600 hover:text-[#0b1c3c] transition">LEARN MORE &rarr;</a>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-16 bg-[#0b1c3c] relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[url('https://louismarineqatar.com/wp-content/uploads/2024/11/banner1-1.jpg')] bg-cover bg-center"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Secure Your Vessel?</h2>
            <p class="text-gray-300 mb-8 max-w-2xl mx-auto text-lg">
                Contact our expert team tailored marine safety solutions and reliable equipment support tailored to your fleet's needs.
            </p>
            <a href="{{ route('contact') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-full transition shadow-lg text-lg">
                Contact Us Now
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#08152e] text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Footer 1 -->
                <div>
                    <img class="h-12 w-auto mb-6 bg-white dark:bg-gray-800 p-2 rounded" src="https://louismarineqatar.com/wp-content/uploads/2024/11/logo.jpg" alt="Louis Marine">
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        We are one of the leading stockist for marine safety, survival and fire fighting equipment in Qatar since 1999.
                    </p>
                    <div class="flex space-x-4">
                        <!-- Social Icons Placeholders -->
                        <a href="#" class="text-gray-400 hover:text-white transition"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                         <a href="#" class="text-gray-400 hover:text-white transition"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.85-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div>
                </div>

                <!-- Footer 2 -->
                <div>
                    <h3 class="text-lg font-bold mb-6 border-l-4 border-red-600 pl-3">Quick Links</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('about') }}" class="hover:text-red-500 transition">Our Story</a></li>
                        <li><a href="{{ route('services.index') }}" class="hover:text-red-500 transition">Services</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-red-500 transition">Products</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">Certificates</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-red-500 transition">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Footer 3 -->
                <div>
                    <h3 class="text-lg font-bold mb-6 border-l-4 border-red-600 pl-3">Services</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-red-500 transition">Fire Fighting Equipment</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">Life Saving Appliances</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">Breathing Apparatus</a></li>
                        <li><a href="#" class="hover:text-red-500 transition">Winch & Liferafts</a></li>
                    </ul>
                </div>

                <!-- Footer 4 -->
                 <div>
                    <h3 class="text-lg font-bold mb-6 border-l-4 border-red-600 pl-3">Contact Us</h3>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-3 mt-1"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>
                            <span>P.O. Box: 9864, Street #24, Industrial Area, Doha, Qatar</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-red-500 mr-3"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></span>
                            <span>+974 4460 3388</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-red-500 mr-3"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                            <span>info@louismarineqatar.com</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} Louis Marine Qatar. All Rights Reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white">Privacy Policy</a>
                    <a href="#" class="hover:text-white">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
