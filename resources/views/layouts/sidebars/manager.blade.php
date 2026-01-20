@php
    $dashboardRoute = route('manager.dashboard');
@endphp

<!-- Manager Sidebar -->
<a href="{{ $dashboardRoute }}" class="flex items-center px-4 py-3 text-sm font-medium transition-colors duration-200 rounded-md group {{ request()->routeIs('manager.dashboard') ? 'bg-accent text-white shadow-lg' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
    {{ __('Overview') }}
</a>

<!-- 1. ACCOUNTS (Operational) -->
<div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Accounts & Finance</div>

<a href="{{ route('accounts.invoices.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.invoices.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    {{ __('Invoices') }}
</a>

<a href="{{ route('accounts.payments.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.payments.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    {{ __('Payments') }}
</a>

<a href="{{ route('accounts.reports.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.reports.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    {{ __('Reports') }}
</a>

<a href="{{ route('accounts.journals.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.journals.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
    {{ __('Journal Entries') }}
</a>

<!-- 2. ENGINEERING -->
<div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Engineering</div>

<a href="{{ route('admin.jobs.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.jobs.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
    {{ __('Service Jobs') }}
</a>

<a href="{{ route('admin.dispatch.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dispatch.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
    {{ __('Dispatch Notes') }}
</a>

<a href="{{ route('admin.certificates.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.certificates.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
    {{ __('Certificates') }}
</a>

<a href="{{ route('admin.customers.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.customers.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
    {{ __('Customers') }}
</a>
        
<a href="{{ route('admin.vessels.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.vessels.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
    {{ __('Vessels') }}
</a>

<!-- 3. STORE / INVENTORY -->
<div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Store & Inventory</div>

<a href="{{ route('storekeeper.inventory.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.inventory.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
    {{ __('Inventory Master') }}
</a>

<a href="{{ route('storekeeper.stock.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.stock.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
    {{ __('Stock Movement') }}
</a>

<a href="{{ route('storekeeper.suppliers.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('storekeeper.suppliers.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
    {{ __('Suppliers') }}
</a>

<!-- 4. MANAGER TOOLS -->
<div class="mt-6 mb-2 px-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Manager Tools</div>

<a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
    {{ __('User Profiles') }}
</a>

<a href="{{ route('admin.finance.payroll.employees.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.finance.payroll.employees.*') ? 'bg-accent text-white' : 'text-gray-300 hover:bg-marine-800 hover:text-white' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
    {{ __('Manage Employees') }}
</a>
