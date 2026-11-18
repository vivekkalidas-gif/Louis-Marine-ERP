@php
    $dashboardRoute = route('manager.dashboard');
@endphp

<!-- Dashboard Overview -->
<a href="{{ $dashboardRoute }}" 
   :title="isCollapsed ? 'Overview' : ''"
   class="flex items-center px-3.5 py-2.5 text-xs font-semibold rounded-lg transition-all duration-150 {{ request()->routeIs('manager.dashboard') ? 'bg-brand-600 text-white shadow-md shadow-brand-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
    <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
    <span x-show="!isCollapsed" class="truncate">{{ __('Overview') }}</span>
</a>

<!-- 1. ACCOUNTS & FINANCE -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Accounts & Finance</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ route('accounts.invoices.index') }}" 
           :title="isCollapsed ? 'Invoices' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('accounts.invoices.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Invoices') }}</span>
        </a>

        <a href="{{ route('accounts.payments.index') }}" 
           :title="isCollapsed ? 'Payments' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('accounts.payments.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Payments') }}</span>
        </a>

        <a href="{{ route('accounts.reports.index') }}" 
           :title="isCollapsed ? 'Reports' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('accounts.reports.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Reports') }}</span>
        </a>

        <a href="{{ route('accounts.journals.index') }}" 
           :title="isCollapsed ? 'Journal Entries' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('accounts.journals.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Journal Entries') }}</span>
        </a>
    </div>
</div>

<!-- 2. ENGINEERING -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Engineering</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ route('admin.jobs.index') }}" 
           :title="isCollapsed ? 'Service Jobs' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.jobs.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Service Jobs') }}</span>
        </a>

        <a href="{{ route('admin.dispatch.index') }}" 
           :title="isCollapsed ? 'Dispatch Notes' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.dispatch.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Dispatch Notes') }}</span>
        </a>

        <a href="{{ route('admin.certificates.index', ['tab' => 'drafts']) }}" 
           :title="isCollapsed ? 'Certificates (Drafts)' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.certificates.*') && request()->query('tab') !== 'issued' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Certificates (Drafts)') }}</span>
        </a>

        <a href="{{ route('admin.certificates.index', ['tab' => 'issued']) }}" 
           :title="isCollapsed ? 'Issued Certificates' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.certificates.*') && request()->query('tab') === 'issued' ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Issued Certificates') }}</span>
        </a>

        <a href="{{ route('admin.customers.index') }}" 
           :title="isCollapsed ? 'Customers' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Customers') }}</span>
        </a>
                
        <a href="{{ route('admin.vessels.index') }}" 
           :title="isCollapsed ? 'Vessels' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.vessels.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Vessels') }}</span>
        </a>
    </div>
</div>

<!-- 3. STORE & INVENTORY -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Store & Inventory</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ route('storekeeper.inventory.index') }}" 
           :title="isCollapsed ? 'Inventory Master' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('storekeeper.inventory.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Inventory Master') }}</span>
        </a>

        <a href="{{ route('storekeeper.stock.index') }}" 
           :title="isCollapsed ? 'Stock Movement' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('storekeeper.stock.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Stock Movement') }}</span>
        </a>

        <a href="{{ route('storekeeper.suppliers.index') }}" 
           :title="isCollapsed ? 'Suppliers' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('storekeeper.suppliers.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Suppliers') }}</span>
        </a>
    </div>
</div>

<!-- 4. MANAGER TOOLS -->
<div class="mt-4">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>Manager Tools</span>
    </div>
    <div class="space-y-0.5">
        <a href="{{ route('admin.roles.index') }}" 
           :title="isCollapsed ? 'Access Control Matrix' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.roles.*') ? 'bg-brand-600 text-white shadow-sm font-bold' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Access Control Matrix') }}</span>
        </a>

        <a href="{{ route('admin.users.index') }}" 
           :title="isCollapsed ? 'User Profiles' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('User Profiles') }}</span>
        </a>

        <a href="{{ route('admin.finance.payroll.employees.index') }}" 
           :title="isCollapsed ? 'Manage Employees' : ''"
           class="flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors {{ request()->routeIs('admin.finance.payroll.employees.*') ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
            <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span x-show="!isCollapsed" class="truncate">{{ __('Manage Employees') }}</span>
        </a>
    </div>
</div>
