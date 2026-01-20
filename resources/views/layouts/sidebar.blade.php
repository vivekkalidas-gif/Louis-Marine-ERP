<div class="flex flex-col h-full">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-marine-950 dark:bg-marine-950 shadow-md">
        <a href="{{ route('dashboard') }}" class="flex items-center px-4">
            @if(config('settings.company_logo'))
                <img src="{{ asset(config('settings.company_logo')) }}" alt="{{ config('settings.company_name') ?? 'Louis Marine' }}" style="height: {{ config('settings.branding_logo_height', '40') }}px; width: auto; max-width: 100%;">
            @else
                <span class="text-xl font-bold text-white uppercase tracking-wider">{{ config('settings.company_name') ?? 'LOUIS MARINE' }}</span>
            @endif
        </a>
    </div>

    <!-- Nav Links (Vertical) -->
    <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto bg-gray-50 dark:bg-marine-900 sidebar-scrollbar" x-data="{ openSection: null }">
        
        @if(auth()->user()->hasRole('super-admin'))
             @include('layouts.sidebars.cms')
        @elseif(auth()->user()->hasRole('manager'))
             @include('layouts.sidebars.manager')
        @else
             @include('layouts.sidebars.default')
        @endif

        <div class="h-10"></div> <!-- Spacer -->

    </nav>
</div>
