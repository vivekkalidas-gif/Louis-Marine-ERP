@if(config('app.certificate_only')) @include('layouts.cert-shell') @else
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Louis Marine ERP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Flatpickr -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        
        <!-- Early Theme Detection -->
        <script>
            if (localStorage.getItem('color-theme') === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
                if (!localStorage.getItem('color-theme')) {
                    localStorage.setItem('color-theme', 'light');
                }
            }
        </script>
    </head>
    <style>
        .sidebar-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1); 
        }
        .sidebar-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569;
            border-radius: 20px;
        }
        .sidebar-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #475569 transparent;
        }
    </style>
    <body class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-100 dark:bg-slate-950" x-data="sidebarManager()">
        <div class="flex h-screen overflow-hidden bg-slate-100 dark:bg-slate-950">
            
            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false" 
                 x-transition:enter="transition-opacity ease-linear duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="transition-opacity ease-linear duration-300" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 z-30 bg-slate-900/60 backdrop-blur-xs lg:hidden" 
                 style="display: none;"></div>

            <!-- Sidebar Container (Collapsible & Resizable) -->
            <div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'" 
                 :style="window.innerWidth >= 1024 ? ('width: ' + (isCollapsed ? '68px' : sidebarWidth + 'px')) : ''"
                 class="fixed inset-y-0 left-0 z-40 transition-all duration-150 transform bg-slate-900 lg:translate-x-0 lg:static lg:inset-0 overflow-hidden shrink-0 relative group">
                
                @include('layouts.sidebar')

                <!-- Drag Handle for Resizing Sidebar Width -->
                <div @mousedown="startResize($event)"
                     class="hidden lg:block absolute top-0 right-0 bottom-0 w-1.5 cursor-col-resize hover:bg-brand-500/80 active:bg-brand-600 z-50 transition-colors opacity-0 hover:opacity-100 group-hover:opacity-60"
                     title="Drag to resize sidebar width">
                </div>

                <!-- Collapse / Expand Floating Arrow Button -->
                <button type="button" 
                        @click="toggleCollapse()" 
                        class="hidden lg:flex absolute top-4 -right-3 w-6 h-6 rounded-full bg-slate-800 border border-slate-700 text-slate-300 hover:text-white items-center justify-center shadow-md z-50 transition-transform hover:scale-110"
                        :title="isCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="isCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>

            <!-- Main Content Area with SINGLE UNIFIED HEADER -->
            <div class="flex-1 flex flex-col overflow-hidden relative min-w-0">
                
                <!-- UNIFIED SINGLE ENTERPRISE HEADER -->
                @include('layouts.header')

                <!-- Main Scrollable Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-100 dark:bg-slate-950">
                    
                    <!-- Global Alert Messages -->
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                        <x-alert-success />
                        <x-alert-error />
                    </div>

                    <!-- Main Page Body -->
                    <div class="py-5 px-4 sm:px-6 lg:px-8">
                        {{ $slot ?? '' }}
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
        
        @unless(config('app.certificate_only'))
            @include('partials.chat-widget')
        @endunless

        <!-- Sidebar Manager Script (Collapse + Manual Drag Resize) -->
        <script>
            function sidebarManager() {
                return {
                    sidebarOpen: false,
                    isCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',
                    sidebarWidth: parseInt(localStorage.getItem('sidebar-width') || '260', 10),
                    isResizing: false,
                    minWidth: 200,
                    maxWidth: 420,

                    init() {
                        if (this.sidebarWidth < this.minWidth) this.sidebarWidth = 260;
                        if (this.sidebarWidth > this.maxWidth) this.sidebarWidth = 420;
                    },

                    toggleCollapse() {
                        this.isCollapsed = !this.isCollapsed;
                        localStorage.setItem('sidebar-collapsed', this.isCollapsed);
                    },

                    startResize(e) {
                        this.isResizing = true;
                        document.body.classList.add('select-none', 'cursor-col-resize');
                        
                        const startX = e.clientX;
                        const startWidth = this.sidebarWidth;

                        const onMouseMove = (moveEvent) => {
                            if (!this.isResizing) return;
                            let newWidth = startWidth + (moveEvent.clientX - startX);
                            if (newWidth < 160) {
                                this.isCollapsed = true;
                            } else {
                                this.isCollapsed = false;
                                if (newWidth > this.maxWidth) newWidth = this.maxWidth;
                                if (newWidth < this.minWidth) newWidth = this.minWidth;
                                this.sidebarWidth = newWidth;
                            }
                        };

                        const onMouseUp = () => {
                            this.isResizing = false;
                            document.body.classList.remove('select-none', 'cursor-col-resize');
                            localStorage.setItem('sidebar-width', this.sidebarWidth);
                            localStorage.setItem('sidebar-collapsed', this.isCollapsed);
                            window.removeEventListener('mousemove', onMouseMove);
                            window.removeEventListener('mouseup', onMouseUp);
                        };

                        window.addEventListener('mousemove', onMouseMove);
                        window.addEventListener('mouseup', onMouseUp);
                    }
                }
            }
        </script>
        
        <!-- Theme Toggle Logic -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const themeToggleBtn = document.getElementById('theme-toggle');
                const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
                const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

                if (themeToggleBtn && themeToggleDarkIcon && themeToggleLightIcon) {
                    if (document.documentElement.classList.contains('dark')) {
                        themeToggleDarkIcon.classList.remove('hidden');
                    } else {
                        themeToggleLightIcon.classList.remove('hidden');
                    }

                    themeToggleBtn.addEventListener('click', function() {
                        themeToggleDarkIcon.classList.toggle('hidden');
                        themeToggleLightIcon.classList.toggle('hidden');

                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    });
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>

@endif
