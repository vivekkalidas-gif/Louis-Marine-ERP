<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Louis Marine') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900 relative">
            <!-- Background Decoration (Optional) -->
            <div class="absolute inset-0 z-0 opacity-5 dark:opacity-20" 
                 style="background-image: radial-gradient(#0b1c3c 1px, transparent 1px); background-size: 20px 20px;"></div>
            
            <div class="w-full max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg z-10 relative">
                 <!-- Logo -->
                <div class="text-center mb-8">
                     @if(config('settings.company_logo'))
                        <img src="{{ asset(config('settings.company_logo')) }}" alt="Logo" class="h-24 w-auto mx-auto drop-shadow-md">
                    @else
                        <h2 class="text-3xl font-bold text-[#0b1c3c] dark:text-white uppercase tracking-widest">{{ config('settings.company_name', 'Louis Marine') }}</h2>
                    @endif
                </div>

                <div class="bg-transparent dark:bg-transparent">
                    <!-- Content Slot -->
                     <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Please sign in to your account</p>
                    </div>

                    {{ $slot }}
                    
                    <div class="mt-8 text-center">
                        <p class="text-xs text-gray-400">
                            &copy; {{ date('Y') }} {{ config('settings.company_name') }}. Secured System.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
