<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Flatpickr -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        
        <!-- Early Theme Detection (Prevent Flash) -->
        <script>
            // Apply theme immediately before page renders
            if (localStorage.getItem('color-theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
                // Default to light mode if no preference is set
                if (!localStorage.getItem('color-theme')) {
                    localStorage.setItem('color-theme', 'light');
                }
            }
        </script>
    </head>
    <style>
        /* Custom Scrollbar for Sidebar */
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1); 
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569; /* Slate 600 */
            border-radius: 20px;
        }
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #475569 transparent;
        }
    </style>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-900" x-data="{ sidebarOpen: false }">
        <div class="flex h-screen overflow-hidden bg-gray-100 dark:bg-gray-900">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-20 bg-gray-900 bg-opacity-50 lg:hidden"></div>

            <!-- Sidebar (Mobile + Desktop) -->
            <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" class="fixed inset-y-0 left-0 z-30 w-64 transition duration-300 transform bg-marine-900 dark:bg-gray-800 lg:translate-x-0 lg:static lg:inset-0 overflow-hidden">
                @include('layouts.sidebar')
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden relative">
                
                <!-- Top Header (Profile/Settings + Hamburger) -->
                <header class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 dark:text-gray-400 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- User Dropdown (Re-using logic from navigation.blade.php or simplifying) -->
                     <div class="flex items-center">
                         @include('layouts.navigation') 
                     </div>
                </header>

                <!-- Main Scrollable Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">
                    <!-- Page Heading -->
                    @isset($header)
                        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </div>
                    @endisset

                    <div class="py-6 px-4 sm:px-6 lg:px-8">
                        {{ $slot ?? '' }}
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        
        @include('partials.chat-widget')
        
        <!-- Theme Toggle Script -->
        <script>
            // Theme toggle functionality
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            // Show correct icon based on current theme
            if (document.documentElement.classList.contains('dark')) {
                themeToggleDarkIcon.classList.remove('hidden');
            } else {
                themeToggleLightIcon.classList.remove('hidden');
            }

            // Toggle theme on button click
            themeToggleBtn.addEventListener('click', function() {
                // Toggle icons
                themeToggleDarkIcon.classList.toggle('hidden');
                themeToggleLightIcon.classList.toggle('hidden');

                // Toggle theme
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
