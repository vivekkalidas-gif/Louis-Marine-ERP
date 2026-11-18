<!-- Dashboard Link (Always Visible) -->
@php
    $dashboardRoute = route('dashboard');
    if(auth()->user()->hasRole('engineer')) $dashboardRoute = route('engineer.dashboard');
    if(auth()->user()->hasRole('storekeeper')) $dashboardRoute = route('storekeeper.dashboard');
    if(auth()->user()->hasRole('manager')) $dashboardRoute = route('manager.dashboard');
    if(auth()->user()->hasRole('cms-manager')) $dashboardRoute = route('admin.cms.dashboard');

    $isCollapsible = false;
@endphp

<a href="{{ $dashboardRoute }}" 
   :title="isCollapsed ? 'Dashboard' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-bold rounded-lg transition-all duration-150 {{ request()->routeIs('*dashboard*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
    <span x-show="!isCollapsed" class="truncate">{{ __('Dashboard') }}</span>
</a>

@if(auth()->user()->can('manage_master_data') || auth()->user()->hasRole(['admin', 'manager', 'accountant', 'storekeeper', 'engineer']))
<!-- MASTER DATA GROUP -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Master Data</span>
    </div>
    <div class="space-y-0.5">
        @if(auth()->user()->can('accounts.view') || auth()->user()->hasRole('accountant'))
            <a href="{{ route('accounts.coa.index') }}" 
               :title="isCollapsed ? 'Chart of Accounts' : ''"
               class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('accounts.coa.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                <span x-show="!isCollapsed" class="truncate">{{ __("Chart of Accounts") }}</span>
            </a>
        @endif
        
        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper']))
        <a href="{{ route('storekeeper.inventory.index') }}" 
           :title="isCollapsed ? 'Inventory Items' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('storekeeper.inventory.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Inventory Items") }}</span>
        </a>
        <a href="{{ route('storekeeper.categories.index') }}" 
           :title="isCollapsed ? 'Categories' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('*categories*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Categories") }}</span>
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'engineer', 'accountant']))
        <a href="{{ route('admin.service-types.index') }}" 
           :title="isCollapsed ? 'Service Types' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.service-types.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Service Types") }}</span>
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper', 'engineer', 'accountant']))
        <a href="{{ route('admin.customers.index') }}" 
           :title="isCollapsed ? 'Customers' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Customers") }}</span>
        </a>
        @endif
        
        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper', 'engineer']))
        <a href="{{ route('admin.vessels.index') }}" 
           :title="isCollapsed ? 'Vessels' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.vessels.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Vessels") }}</span>
        </a>
        <a href="{{ route('admin.equipment.categories.index') }}" 
           :title="isCollapsed ? 'Equipment Categories' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.equipment.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Equipment Categories") }}</span>
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper']))
        <a href="{{ route('storekeeper.suppliers.index') }}" 
           :title="isCollapsed ? 'Suppliers' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('storekeeper.suppliers.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Suppliers") }}</span>
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'engineer']))
        <a href="{{ route('admin.certificate-templates.index') }}" 
           :title="isCollapsed ? 'Certificate Templates' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.certificate-templates.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Certificate Templates") }}</span>
        </a>
        <a href="{{ route('admin.certificates.index', ['tab' => 'drafts']) }}" 
           :title="isCollapsed ? 'Certificates (Drafts)' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.certificates.*') && request()->query('tab') !== 'issued' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Certificates (Drafts)") }}</span>
        </a>
        <a href="{{ route('admin.certificates.index', ['tab' => 'issued']) }}" 
           :title="isCollapsed ? 'Issued Certificates' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.certificates.*') && request()->query('tab') === 'issued' ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Issued Certificates") }}</span>
        </a>
        @endif
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'manager', 'engineer', 'storekeeper']))
<!-- OPERATIONS GROUP -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Operations</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ auth()->user()->hasRole('engineer') ? route('engineer.jobs.index') : route('admin.jobs.index') }}" 
           :title="isCollapsed ? 'Service Jobs' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs(['admin.jobs.*', 'engineer.jobs.*']) ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Service Jobs") }}</span>
        </a>
        
        <a href="{{ route('admin.dispatch.index') }}" 
           :title="isCollapsed ? 'Dispatch Notes' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.dispatch.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Dispatch Notes") }}</span>
        </a>
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'manager', 'super-admin']))
<!-- ADMINISTRATION & ACCESS CONTROL GROUP -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Administration & Security</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ route('admin.roles.index') }}" 
           :title="isCollapsed ? 'Access Control Matrix' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Access Control Matrix") }}</span>
        </a>

        <a href="{{ route('admin.users.index') }}" 
           :title="isCollapsed ? 'User Accounts' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("User Accounts") }}</span>
        </a>

        <a href="{{ route('admin.settings.index') }}" 
           :title="isCollapsed ? 'System Settings' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("System Settings") }}</span>
        </a>
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'manager', 'super-admin', 'cms-manager']))
<!-- CMS & LANDING PAGE GROUP -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Website & CMS</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ route('admin.cms.dashboard') }}" 
           :title="isCollapsed ? 'CMS Dashboard' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.cms.dashboard') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("CMS Dashboard") }}</span>
        </a>

        <a href="{{ route('admin.cms.landing-page.hero-slides.index') }}" 
           :title="isCollapsed ? 'Landing Page Editor' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.cms.landing-page.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Landing Page Editor") }}</span>
        </a>

        <a href="{{ route('admin.cms.services.index') }}" 
           :title="isCollapsed ? 'Services & Offerings' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.cms.services.*') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __("Services & Offerings") }}</span>
        </a>
    </div>
</div>
@endif
