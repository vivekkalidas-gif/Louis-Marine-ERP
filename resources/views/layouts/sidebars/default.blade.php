<!-- Dashboard Link (Always Visible) -->
@php
    $dashboardRoute = route('dashboard');
    if(auth()->user()->hasRole('engineer')) $dashboardRoute = route('engineer.dashboard');
    if(auth()->user()->hasRole('storekeeper')) $dashboardRoute = route('storekeeper.dashboard');
    if(auth()->user()->hasRole('manager')) $dashboardRoute = route('manager.dashboard');
    if(auth()->user()->hasRole('cms-manager')) $dashboardRoute = route('admin.cms.dashboard');

    // Collapsible logic: Only for Accounts module/dashboard
    // Collapsible logic: Disabled as per user request
    $isCollapsible = false;
@endphp

<a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-md {{ request()->routeIs('*dashboard*') ? 'bg-accent text-white shadow-lg' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
    {{ __('Dashboard') }}
</a>

@if(auth()->user()->can('manage_master_data') || auth()->user()->hasRole(['admin', 'manager', 'accountant', 'storekeeper', 'engineer']))
<!-- MASTER DATA GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'master' ? null : 'master'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Master Data</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'master' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'master'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        @if(auth()->user()->can('accounts.view') || auth()->user()->hasRole('accountant'))
            <a href="{{ route('accounts.coa.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.coa.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>{{ __("Chart of Accounts") }}
            </a>
        @endif
        
        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper']))
        <a href="{{ route('storekeeper.inventory.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.inventory.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>{{ __("Inventory Items") }}
        </a>
        <a href="{{ route('storekeeper.categories.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('*categories*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>{{ __("Categories") }}
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'engineer', 'accountant']))
        <a href="{{ route('admin.service-types.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.service-types.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>{{ __("Service Types") }}
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper', 'engineer', 'accountant']))
        <a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.customers.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>{{ __("Customers") }}
        </a>
        @endif
        
        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper', 'engineer']))
        <a href="{{ route('admin.vessels.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.vessels.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>{{ __("Vessels") }}
        </a>
        <a href="{{ route('admin.equipment.categories.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.equipment.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>{{ __("Service Categories") }}
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper']))
        <a href="{{ route('storekeeper.suppliers.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.suppliers.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>{{ __("Suppliers") }}
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'manager', 'engineer']))
        <a href="{{ route('admin.certificate-templates.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.certificate-templates.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>{{ __("Certificate Master") }}
        </a>
        <a href="{{ route('admin.certificates.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.certificates.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>{{ __("Certificates") }}
        </a>
        @endif

        @if(auth()->user()->hasRole(['admin', 'accountant']))
        <a href="{{ route('admin.finance.payroll.employees.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.finance.payroll.employees.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ __("Payroll Master") }}
        </a>
        @endif
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'manager', 'engineer', 'storekeeper']))
<!-- OPERATIONS GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'operations' ? null : 'operations'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Operations</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'operations' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'operations'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ auth()->user()->hasRole('engineer') ? route('engineer.jobs.index') : route('admin.jobs.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs(['admin.jobs.*', 'engineer.jobs.*']) ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>{{ __("Service Jobs") }}
        </a>
        
        <a href="{{ route('admin.dispatch.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dispatch.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>{{ __("Dispatch Notes") }}
        </a>

        {{-- @if(auth()->user()->hasRole('engineer') && Route::has('engineer.equipment.index'))
        <a href="{{ route('engineer.equipment.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('engineer.equipment.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ __("Equipment List") }}
        </a>
        @endif --}}

        @if(auth()->user()->hasRole('engineer'))
        <a href="{{ route('engineer.tools.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('engineer.tools.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ __("My Tools") }}
        </a>
        @endif

        @if(auth()->user()->hasRole(['storekeeper', 'admin', 'manager']))
        <a href="{{ route('storekeeper.tools.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.tools.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>{{ __("Tools & Equipment") }}
        </a>
        @endif
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'manager', 'storekeeper']))
<!-- INVENTORY GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'inventory' ? null : 'inventory'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Inventory</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'inventory' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'inventory'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ route('storekeeper.stock.inward') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.stock.inward') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path></svg>{{ __("Stock Inward") }}
        </a>
        <a href="{{ route('storekeeper.stock.adjustment') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.stock.adjustment') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>{{ __("Stock Adjustment") }}
        </a>
        <a href="{{ route('storekeeper.stock.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.stock.index') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>{{ __("Stock Movements") }}
        </a>
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'accountant', 'storekeeper']))
<!-- SALES GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'sales' ? null : 'sales'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Sales</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'sales' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'sales'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ route('storekeeper.rfq.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.rfq.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>{{ __("Requests for Quotes") }}
        </a>
        <a href="{{ route('admin.dispatch.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dispatch.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>{{ __("Dispatch Notes") }}
        </a>
        <a href="{{ route('admin.jobs.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.jobs.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>{{ __("Service Jobs") }}
        </a>
        @if(auth()->user()->hasRole(['storekeeper', 'admin', 'manager']))
        <a href="{{ route('storekeeper.quotations.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.quotations.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>{{ __("Quotations") }}
        </a>
        @endif
        @if(auth()->user()->hasRole(['storekeeper', 'admin', 'manager']))
        <a href="{{ route('storekeeper.delivery-notes.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.delivery-notes.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ __("Delivery Notes") }}
        </a>
        @endif
        @if(auth()->user()->hasRole(['admin', 'accountant']))
        @if(Route::has('accounts.invoices.index'))
        <a href="{{ route('accounts.invoices.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.invoices.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path></svg>{{ __("Invoices") }}
        </a>
        @endif
        @if(Route::has('accounts.payments.index'))
        <a href="{{ route('accounts.payments.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.payments.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>{{ __("Payments") }}
        </a>
        @endif
        @if(Route::has('accounts.ar.index'))
        <a href="{{ route('accounts.ar.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.ar.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ __('Accounts Receivable') }}
        </a>
        @endif
        @endif
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'accountant', 'storekeeper']))
<!-- PURCHASE GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'purchase' ? null : 'purchase'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Purchase</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'purchase' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'purchase'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ route('storekeeper.purchase-orders.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.purchase-orders.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>{{ __("Purchase Orders") }}
        </a>
        <a href="{{ route('storekeeper.suppliers.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.suppliers.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>{{ __("Suppliers") }}
        </a>
        @if(auth()->user()->hasRole(['admin', 'accountant']))
        @if(Route::has('accounts.vendor-bills.index'))
        <a href="{{ route('accounts.vendor-bills.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.vendor-bills.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>{{ __('Vendor Bills') }}
        </a>
        @endif
        @if(Route::has('accounts.vendor-payments.index'))
        <a href="{{ route('accounts.vendor-payments.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.vendor-payments.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
             <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>{{ __('Vendor Payments') }}
        </a>
        @endif
        @endif
    </div>
</div>
@endif

@if(auth()->user()->hasRole(['admin', 'accountant']))
<!-- BANKING & CASH GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'banking' ? null : 'banking'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Banking & Cash</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'banking' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'banking'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ route('accounts.cash-bank-book.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.cash-bank-book.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ __('Cash & Bank Book') }}
        </a>
        <a href="{{ route('accounts.reconciliation.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.reconciliation.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>{{ __('Bank Reconciliation') }}
        </a>
    </div>
</div>

<!-- ACCOUNTING CORE GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'accounting' ? null : 'accounting'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Accounting Core</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'accounting' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'accounting'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ route('accounts.journals.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.journals.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>{{ __("Journal Entries") }}
        </a>
        <a href="{{ route('accounts.periods.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.periods.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>{{ __('Accounting Periods') }}
        </a>
        <a href="{{ route('admin.finance.incomes.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.finance.incomes.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
           <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>{{ __('Income') }}
        </a>
        <a href="{{ route('admin.finance.expenses.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.finance.expenses.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>{{ __("Expenses") }}
        </a>
    </div>
</div>

<!-- REPORTS GROUP -->
<div class="mt-2">
    <button @if($isCollapsible) @click="openSection = openSection === 'reports' ? null : 'reports'" @endif class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-500 uppercase tracking-wider hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-marine-800 rounded-md transition-colors duration-200">
        <span>Reports</span>
        @if($isCollapsible)
        <svg class="w-4 h-4 transform transition-transform duration-200" :class="openSection === 'reports' ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        @endif
    </button>
    <div @if($isCollapsible) x-show="openSection === 'reports'" x-collapse @endif class="pl-2 space-y-1 mt-1">
        <a href="{{ route('accounts.reports.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.reports.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>{{ __("Reports") }}
        </a>
    </div>
</div>
@endif
