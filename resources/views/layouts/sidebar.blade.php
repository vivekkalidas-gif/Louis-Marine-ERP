<div class="flex flex-col h-full bg-slate-900 text-slate-100 relative">
    <!-- Logo Header -->
    <div class="flex items-center justify-between h-16 px-4 bg-slate-950/80 border-b border-slate-800 shadow-sm shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 overflow-hidden">
            @if(config('settings.company_logo'))
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(config('settings.company_logo')) }}" alt="{{ config('settings.company_name') ?? 'Louis Marine' }}" style="height: {{ config('settings.branding_logo_height', '36') }}px; width: auto; max-width: 100%;">
            @else
                <div class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shadow-md shrink-0">LM</div>
                <span x-show="!isCollapsed" x-transition.opacity.duration.150ms class="text-sm font-black tracking-wider text-white uppercase truncate whitespace-nowrap">{{ config('settings.company_name') ?? 'LOUIS MARINE QATAR' }}</span>
            @endif
        </a>
    </div>

    <!-- Nav Links (Vertical Scrollable) -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto sidebar-scrollbar" x-data="{ openSection: null }">
        @if(config('app.certificate_only'))
             @include('layouts.sidebars.certificates')
        @elseif(auth()->user()->hasRole('super-admin'))
             @include('layouts.sidebars.cms')
        @elseif(auth()->user()->hasRole('manager'))
             @include('layouts.sidebars.manager')
        @else
             @include('layouts.sidebars.default')
        @endif

        <div class="h-8"></div>
    </nav>

    <!-- Footer Profile Summary -->
    <div class="p-3 border-t border-slate-800 bg-slate-950/50 shrink-0">
        <div class="flex items-center gap-3 px-2 py-1.5 rounded-lg bg-slate-800/60 border border-slate-700/50" :title="isCollapsed ? '{{ auth()->user()->name }}' : ''">
            <div class="w-8 h-8 rounded-full bg-brand-600/80 text-white font-bold text-xs flex items-center justify-center uppercase shadow-sm shrink-0">
                {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
            </div>
            <div x-show="!isCollapsed" x-transition.opacity.duration.150ms class="flex-1 min-w-0">
                <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-400 truncate capitalize">{{ auth()->user()->roles->first()?->name ?? 'User' }}</p>
            </div>
        </div>
    </div>
</div>
