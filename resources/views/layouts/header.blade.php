<header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-2xs sticky top-0 z-20">
    <div class="px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4 min-h-[64px] lg:min-h-[76px]">
        
        <!-- LEFT SIDE: Mobile Toggle + Breadcrumbs & Integrated Page Title -->
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <!-- Mobile Hamburger Toggle -->
            <button @click="sidebarOpen = true" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Desktop Sidebar Collapse Toggle -->
            <button type="button" @click="toggleCollapse()" class="hidden lg:flex p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 shrink-0 transition-colors" title="Toggle Sidebar Width (Collapsible / Expandable)">
                <svg class="w-5 h-5 transition-transform duration-200" :class="isCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>

            <div class="flex flex-col min-w-0">
                <!-- Dynamic Breadcrumbs -->
                <div class="mb-0.5">
                    <x-breadcrumbs />
                </div>
                
                <!-- Title & Subtitle or Custom Header Slot Content -->
                @isset($header)
                    <div class="header-slot-wrapper">
                        {{ $header }}
                    </div>
                @endisset
            </div>
        </div>

        <!-- CENTER: Optional Global Search Bar -->
        <div class="hidden xl:flex items-center flex-1 max-w-xs mx-4">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="search" placeholder="Search orders, vessels, certificates... (Ctrl+K)" class="w-full text-xs pl-9 pr-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-800/60 text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 placeholder-slate-400 transition-all">
            </div>
        </div>

        <!-- RIGHT SIDE: Notifications + Theme Toggle + User Profile Dropdown -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            
            <!-- Notifications Bell Dropdown -->
            <x-dropdown align="right" width="80">
                <x-slot name="trigger">
                    <button class="relative p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full ring-2 ring-white dark:ring-slate-900 bg-rose-500"></span>
                        @endif
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2.5 border-b border-slate-200 dark:border-slate-700/80 font-bold text-xs text-slate-900 dark:text-white flex items-center justify-between">
                        <span>Notifications</span>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="badge-neutral text-[10px]">{{ auth()->user()->unreadNotifications->count() }} new</span>
                        @endif
                    </div>
                    
                    @if(auth()->check())
                        <div class="max-h-64 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors border-b border-slate-100 dark:border-slate-800">
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $notification->data['message'] ?? '' }}
                                    </p>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-xs text-slate-400 text-center">
                                    No unread notifications
                                </div>
                            @endforelse
                        </div>
                    @endif
                </x-slot>
            </x-dropdown>

            <!-- Theme Switcher Button -->
            <button id="theme-toggle" type="button" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </button>

            <!-- Vertical Divider -->
            <div class="h-6 border-r border-slate-200 dark:border-slate-800"></div>

            <!-- Profile Menu Dropdown -->
            @if(auth()->check())
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2.5 p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 text-white font-black text-xs flex items-center justify-center uppercase shadow-xs">
                            {{ substr(Auth::user()->name ?? 'U', 0, 2) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-slate-400 capitalize leading-none mt-1">{{ Auth::user()->roles->first()?->name ?? 'Staff' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 border-b border-slate-200 dark:border-slate-700/80">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <x-dropdown-link :href="route('profile.edit')" class="text-xs">
                        {{ __('My Profile & Settings') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" class="text-xs text-rose-600 dark:text-rose-400 font-semibold">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
            @endif
        </div>
    </div>
</header>
