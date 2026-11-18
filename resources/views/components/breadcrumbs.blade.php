@props(['crumbs' => null])

@php
if (!$crumbs) {
    $routeName = request()->route() ? request()->route()->getName() : '';
    $crumbs = [];
    
    // Base root based on role
    if (auth()->check() && auth()->user()->hasRole('engineer')) {
        $crumbs[] = ['label' => 'Engineer Portal', 'url' => route('engineer.dashboard')];
    } elseif (auth()->check() && auth()->user()->hasRole('storekeeper')) {
        $crumbs[] = ['label' => 'Storekeeper Portal', 'url' => route('storekeeper.dashboard')];
    } else {
        $crumbs[] = ['label' => 'Dashboard', 'url' => route('dashboard')];
    }

    if (str_contains($routeName, 'service-types')) {
        $crumbs[] = ['label' => 'Master Data', 'url' => '#'];
        $crumbs[] = ['label' => 'Service Types', 'url' => route('admin.service-types.index')];
        if (str_contains($routeName, 'create')) $crumbs[] = ['label' => 'Add New', 'url' => ''];
        if (str_contains($routeName, 'edit')) $crumbs[] = ['label' => 'Edit', 'url' => ''];
    } elseif (str_contains($routeName, 'customers')) {
        $crumbs[] = ['label' => 'Customers', 'url' => route('admin.customers.index')];
        if (str_contains($routeName, 'create')) $crumbs[] = ['label' => 'Create Account', 'url' => ''];
        if (str_contains($routeName, 'edit')) $crumbs[] = ['label' => 'Edit', 'url' => ''];
    } elseif (str_contains($routeName, 'vessels')) {
        $crumbs[] = ['label' => 'Fleet Vessels', 'url' => route('admin.vessels.index')];
        if (str_contains($routeName, 'create')) $crumbs[] = ['label' => 'Add Vessel', 'url' => ''];
        if (str_contains($routeName, 'edit')) $crumbs[] = ['label' => 'Edit', 'url' => ''];
    } elseif (str_contains($routeName, 'jobs')) {
        $prefix = request()->routeIs('engineer.*') ? 'engineer' : 'admin';
        $crumbs[] = ['label' => 'Service Jobs', 'url' => route($prefix . '.jobs.index')];
        if (str_contains($routeName, 'create')) $crumbs[] = ['label' => 'New Order', 'url' => ''];
        if (str_contains($routeName, 'show')) $crumbs[] = ['label' => 'Job Details', 'url' => ''];
    } elseif (str_contains($routeName, 'certificates')) {
        $prefix = request()->routeIs('engineer.*') ? 'engineer' : 'admin';
        $crumbs[] = ['label' => 'Certificates', 'url' => route($prefix . '.certificates.index')];
        if (str_contains($routeName, 'create')) $crumbs[] = ['label' => 'New Certificate', 'url' => ''];
    } elseif (str_contains($routeName, 'equipment')) {
        $crumbs[] = ['label' => 'Equipment Locker', 'url' => route('engineer.equipment.index')];
    } elseif (str_contains($routeName, 'rfq')) {
        $crumbs[] = ['label' => 'Requisitions & RFQ', 'url' => route('storekeeper.rfq.index')];
        if (str_contains($routeName, 'create')) $crumbs[] = ['label' => 'Create RFQ', 'url' => ''];
    }
}
@endphp

<nav class="flex items-center text-[11px] font-semibold text-slate-500 dark:text-slate-400 space-x-1.5" aria-label="Breadcrumb">
    @foreach($crumbs as $index => $crumb)
        @if($index > 0)
            <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        @endif
        @if(!empty($crumb['url']) && $crumb['url'] !== '#')
            <a href="{{ $crumb['url'] }}" class="hover:text-brand-600 dark:hover:text-brand-400 transition-colors">
                {{ $crumb['label'] }}
            </a>
        @else
            <span class="text-slate-900 dark:text-slate-200 font-bold">
                {{ $crumb['label'] }}
            </span>
        @endif
    @endforeach
</nav>
