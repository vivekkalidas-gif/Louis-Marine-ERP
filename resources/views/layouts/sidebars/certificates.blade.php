{{-- Sidebar used when CERTIFICATE_ONLY=true --}}
@php
    $link = 'flex items-center px-3.5 py-2 text-xs font-semibold rounded-lg transition-colors';
    $active = 'bg-brand-600 text-white shadow-md shadow-brand-600/20 font-bold';
    $idle = 'text-slate-300 hover:text-white hover:bg-slate-800/70';
    $doc = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
    $items = [
        'Certificates' => [
            ['Certificates (Drafts)', route('admin.certificates.index', ['tab' => 'drafts']), request()->routeIs('admin.certificates.*') && request()->query('tab') !== 'issued', $doc],
            ['Issued Certificates', route('admin.certificates.index', ['tab' => 'issued']), request()->routeIs('admin.certificates.*') && request()->query('tab') === 'issued', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Certificate Templates', route('admin.certificate-templates.index'), request()->routeIs('admin.certificate-templates.*'), $doc],
        ],
        'Master Data' => [
            ['Customers', route('admin.customers.index'), request()->routeIs('admin.customers.*'), 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['Vessels', route('admin.vessels.index'), request()->routeIs('admin.vessels.*'), 'M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9'],
            ['Equipment', route('admin.equipment.categories.index'), request()->routeIs('admin.equipment.*'), 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ],
    ];
    if (auth()->user()->hasRole(['super-admin', 'admin'])) {
        $items['Administration'] = [
            ['User Accounts', route('admin.users.index'), request()->routeIs('admin.users.*'), 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['Settings', route('admin.settings.index'), request()->routeIs('admin.settings.*'), 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
        ];
    }
@endphp

@foreach($items as $group => $links)
<div class="{{ $loop->first ? '' : 'mt-4' }}">
    <div class="px-3.5 mb-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider" x-show="!isCollapsed">
        <span>{{ $group }}</span>
    </div>
    <div class="space-y-0.5">
        @foreach($links as [$label, $url, $isActive, $icon])
            <a href="{{ $url }}" :title="isCollapsed ? '{{ $label }}' : ''" class="{{ $link }} {{ $isActive ? $active : $idle }}">
                <svg class="w-4 h-4 mr-2.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path></svg>
                <span x-show="!isCollapsed" class="truncate">{{ __($label) }}</span>
            </a>
        @endforeach
    </div>
</div>
@endforeach
