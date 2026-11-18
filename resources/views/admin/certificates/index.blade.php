@if(config('app.certificate_only')) @include('admin.certificates.index-glass') @else
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4" x-data="{ currentTab: new URLSearchParams(window.location.search).get('tab') || 'drafts' }">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    <span x-show="currentTab !== 'issued'">{{ __('Certificate Management (Drafts & Pending)') }}</span>
                    <span x-show="currentTab === 'issued'" x-cloak>{{ __('Issued Certificates') }}</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    <span x-show="currentTab !== 'issued'">Manage draft reviews, verification, and issuance workflows</span>
                    <span x-show="currentTab === 'issued'" x-cloak>View and manage all official issued certificates</span>
                </p>
            </div>
            <a href="{{ route('admin.certificates.create') }}" class="btn-primary text-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create New Certificate</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ tab: new URLSearchParams(window.location.search).get('tab') || 'drafts' }">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="theme-card p-0 overflow-hidden shadow-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                
                <!-- Modern Segmented Tabs Header with Solid Dark Mode Colors -->
                <div class="flex border-b border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-950 p-2 gap-2">
                    <button @click="tab = 'drafts'; const url = new URL(window.location); url.searchParams.set('tab', 'drafts'); window.history.pushState({}, '', url);" 
                            :class="{ 'bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 shadow-md border border-slate-200 dark:border-slate-700': tab === 'drafts', 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60': tab !== 'drafts' }"
                            class="flex-1 py-2.5 px-4 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <span>Certificate Control (Drafts / Pending)</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-brand-100 dark:bg-brand-950/80 text-brand-700 dark:text-brand-300 border border-brand-300 dark:border-brand-800/60">{{ count($drafts) }}</span>
                    </button>
                    <button @click="tab = 'issued'; const url = new URL(window.location); url.searchParams.set('tab', 'issued'); window.history.pushState({}, '', url);" 
                            :class="{ 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md border border-slate-200 dark:border-slate-700': tab === 'issued', 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200/60 dark:hover:bg-slate-800/60': tab !== 'issued' }"
                            class="flex-1 py-2.5 px-4 rounded-lg font-bold text-xs transition-all flex items-center justify-center gap-2.5 cursor-pointer">
                        <svg class="w-4 h-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        <span>Issued Certificates (Completed)</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800/60">{{ count($issued) }}</span>
                    </button>
                </div>

                <div class="p-6 bg-white dark:bg-slate-900">
                    <!-- Drafts Tab Table -->
                    <div x-show="tab === 'drafts'">
                        <div class="table-container shadow-sm border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
                            <table class="theme-table">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        <th class="px-6 py-3.5">Ref #</th>
                                        <th class="px-6 py-3.5">Category / Type</th>
                                        <th class="px-6 py-3.5">Vessel & Customer</th>
                                        <th class="px-6 py-3.5">Status</th>
                                        <th class="px-6 py-3.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @forelse($drafts as $cert)
                                        <tr class="theme-table-row hover:bg-slate-50 dark:hover:bg-slate-800/70 transition-colors">
                                            <td class="px-6 py-4">
                                                <span class="font-mono text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-100/80 dark:bg-amber-950/80 border border-amber-300 dark:border-amber-800/60 px-2.5 py-1 rounded-md">
                                                    DRAFT
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">
                                                {{ $cert->type ? $cert->type->name : ($cert->certificateTemplate?->name ?? 'Certificate') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $cert->vessel->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $cert->customer->name ?? 'Unknown Customer' }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $cert->status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right space-x-2">
                                                <a href="{{ route('admin.certificates.show', $cert->id) }}" class="btn-ghost btn-sm text-brand-600 dark:text-brand-400 font-bold">Preview</a>
                                                <a href="{{ route('admin.certificates.edit', $cert->id) }}" class="btn-secondary btn-sm font-bold">Edit</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                                No draft certificates found. <a href="{{ route('admin.certificates.create') }}" class="text-brand-600 dark:text-brand-400 font-semibold hover:underline">Create a new certificate?</a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Issued Tab Table -->
                    <div x-show="tab === 'issued'" x-cloak x-data="{ search: '' }">
                        <!-- Search Bar & Filters Header (Solid Colors for Dark Mode) -->
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-4 bg-slate-100 dark:bg-slate-950 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-xs">
                            <div class="relative flex-1 min-w-[280px] max-w-md">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" x-model="search" class="w-full pl-10 pr-4 py-2 text-xs rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all" placeholder="Search issued certificates by number, vessel, customer...">
                            </div>
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 font-medium">
                                <span>Showing <strong class="text-slate-900 dark:text-slate-100 font-bold">{{ count($issued) }}</strong> Official Certificates</span>
                            </div>
                        </div>

                        <div class="table-container shadow-sm border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
                            <table class="theme-table">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                        <th class="px-6 py-3.5">Cert #</th>
                                        <th class="px-6 py-3.5">Type</th>
                                        <th class="px-6 py-3.5">Vessel / Customer</th>
                                        <th class="px-6 py-3.5">Issued Date</th>
                                        <th class="px-6 py-3.5">Expiry Date</th>
                                        <th class="px-6 py-3.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @forelse($issued as $cert)
                                        <tr x-show="!search || $el.textContent.toLowerCase().includes(search.toLowerCase())" class="theme-table-row hover:bg-slate-50 dark:hover:bg-slate-800/70 transition-colors">
                                            <td class="px-6 py-4">
                                                <span class="font-mono font-bold text-xs text-emerald-700 dark:text-emerald-300 bg-emerald-100/80 dark:bg-emerald-950/80 border border-emerald-300 dark:border-emerald-800/60 px-2.5 py-1 rounded-md tracking-wider">
                                                    {{ str_replace('₂', '2', $cert->certificate_number) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-semibold text-slate-900 dark:text-white">
                                                {{ $cert->type?->name ?? $cert->certificateTemplate?->name ?? 'Certificate' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $cert->vessel->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $cert->customer->name ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                                {{ $cert->issue_date ? $cert->issue_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-xs font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">
                                                {{ $cert->expiry_date ? $cert->expiry_date->format('d M Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end gap-2">
                                                    <a href="{{ route('admin.certificates.show', $cert->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-emerald-500/50 hover:text-emerald-600 dark:hover:text-emerald-400 transition-all shadow-xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        <span>View / PDF</span>
                                                    </a>
                                                    <button 
                                                        @click="$dispatch('open-modal', 'confirm-renew-modal'); $dispatch('set-renew-action', '{{ route('admin.certificates.renew', $cert->id) }}')"
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-600/10 hover:bg-brand-600 text-brand-600 dark:text-brand-400 hover:text-white border border-brand-500/30 transition-all shadow-xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        <span>RENEW</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                                No issued certificates found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Renew Confirmation Modal -->
    <x-modal name="confirm-renew-modal" focusable>
        <form method="POST" x-data="{ action: '' }" @set-renew-action.window="action = $event.detail" :action="action" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                {{ __('Renew Certificate') }}
            </h2>

            <p class="text-sm text-slate-600 dark:text-slate-400">
                {{ __('Are you sure you want to renew this certificate? A new draft will be created with all details pre-filled.') }}
            </p>

            <div class="pt-4 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <button type="submit" class="btn-primary text-xs">
                    {{ __('Renew Certificate') }}
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>

@endif
