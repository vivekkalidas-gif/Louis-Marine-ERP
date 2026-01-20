<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Louis Marine Qatar') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Montserrat', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-700 text-slate-800 flex flex-col min-h-screen">

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
                    <a href="{{ route('home') }}">
                        <img class="h-16 w-auto" src="https://louismarineqatar.com/wp-content/uploads/2024/11/logo.jpg" alt="Louis Marine Qatar">
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8 items-center font-bold text-[#0b1c3c]">
                    <a href="{{ route('home') }}" class="hover:text-red-600 transition {{ request()->routeIs('home') ? 'text-red-600' : '' }}">HOME</a>
                    <a href="{{ route('about') }}" class="hover:text-red-600 transition {{ request()->routeIs('about') ? 'text-red-600' : '' }}">ABOUT US</a>
                    <a href="{{ route('services.index') }}" class="hover:text-red-600 transition {{ request()->routeIs('services.*') ? 'text-red-600' : '' }}">SERVICES</a>
                    <a href="{{ route('products.index') }}" class="hover:text-red-600 transition {{ request()->routeIs('products.*') ? 'text-red-600' : '' }}">PRODUCTS</a>
                    <a href="{{ route('contact') }}" class="hover:text-red-600 transition {{ request()->routeIs('contact') ? 'text-red-600' : '' }}">CONTACT US</a>
                </nav>

                <!-- Quote Cart Indicator -->
                <div class="hidden md:flex items-center mr-4">
                    <a href="{{ route('quote.index') }}" class="relative text-[#0b1c3c] hover:text-red-600 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        @if(session()->has('quote_cart') && count(session('quote_cart')) > 0)
                            <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-bounce">
                                {{ count(session('quote_cart')) }}
                            </span>
                        @endif
                    </a>
                </div>

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

    <!-- Page Content -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[#08152e] text-white pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Footer 1 -->
                <div>
                    <img class="h-12 w-auto mb-6 bg-white dark:bg-gray-800 p-2 rounded" src="https://louismarineqatar.com/wp-content/uploads/2024/11/logo.jpg" alt="Louis Marine">
                    <p class="text-gray-400 text-sm leading-relaxed mb-6">
                        We are one of the leading stockist for marine safety, survival and fire fighting equipment in Qatar since 1999.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.85-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                    </div>
                </div>

                <!-- Footer 2 -->
                <div>
                    <h3 class="text-lg font-bold mb-6 border-l-4 border-red-600 pl-3">Quick Links</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-red-500 transition">Our Story</a></li>
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
                            <span>{{ $globalSettings['company_address'] ?? 'P.O. Box: 9864, Street #24, Industrial Area, Doha, Qatar' }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-red-500 mr-3"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                            <span>{{ $globalSettings['company_phone'] ?? '+974 4460 3388' }}</span>
                        </li>
                        <li class="flex items-center">
                            <span class="text-red-500 mr-3"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></span>
                            <span>{{ $globalSettings['company_email'] ?? 'info@louismarineqatar.com' }}</span>
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
