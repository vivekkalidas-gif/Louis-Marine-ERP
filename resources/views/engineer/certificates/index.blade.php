<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Equipment Certificates') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Create, review, and verify safety inspection & calibration certificates</p>
            </div>
            <a href="{{ route('engineer.certificates.create') }}" class="btn-primary text-xs gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Certificate</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="theme-card p-0 overflow-hidden">
                
                <!-- Segmented Control Filters -->
                <div class="flex border-b border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 p-2 gap-2">
                    <a href="{{ route('engineer.certificates.index') }}" 
                       class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ !request('filter') ? 'bg-white dark:bg-slate-800 text-brand-600 dark:text-brand-400 shadow-xs border border-slate-200/80 dark:border-slate-700/80' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>My Certificates</span>
                        <span class="badge-neutral text-[10px] px-2 py-0.5">{{ $certificates->total() }}</span>
                    </a>
                    <a href="{{ route('engineer.certificates.index', ['filter' => 'to_verify']) }}" 
                       class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2 {{ request('filter') === 'to_verify' ? 'bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 shadow-xs border border-slate-200/80 dark:border-slate-700/80' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white' }}">
                        <span>Assigned to Verify</span>
                        @if(isset($pendingCount) && $pendingCount > 0)
                            <span class="badge-danger text-[10px] px-2 py-0.5 animate-pulse">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>

                <!-- Table Content -->
                <div class="p-6">
                    <div class="table-container">
                        <table class="theme-table">
                            <thead>
                                <tr class="theme-table-header">
                                    <th class="px-6 py-3.5">Certificate No</th>
                                    <th class="px-6 py-3.5">Template / Category</th>
                                    <th class="px-6 py-3.5">Customer & Vessel</th>
                                    <th class="px-6 py-3.5">Validity Dates</th>
                                    <th class="px-6 py-3.5">Status</th>
                                    <th class="px-6 py-3.5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700/60">
                                @forelse($certificates as $cert)
                                <tr class="theme-table-row">
                                    <td class="px-6 py-4">
                                        <div class="font-mono text-xs font-bold text-slate-900 dark:text-white">{{ $cert->certificate_number ?? 'DRAFT' }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">By: {{ $cert->creator->name ?? 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $cert->certificateTemplate->name ?? ($cert->type->name ?? 'Certificate') }}</div>
                                        <div class="text-slate-400 dark:text-slate-500 mt-0.5">{{ $cert->equipmentCategory->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $cert->customer->company_name ?? 'Unknown Customer' }}</div>
                                        <div class="text-slate-500 dark:text-slate-400 mt-0.5">Vessel: {{ $cert->vessel->name ?? 'Unknown Vessel' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        @if($cert->issue_date)
                                            <div class="text-slate-800 dark:text-slate-200 font-medium">Issued: {{ $cert->issue_date->format('d M, Y') }}</div>
                                        @endif
                                        @if($cert->expiry_date)
                                            <div class="text-slate-500 dark:text-slate-400 mt-0.5">Expires: {{ $cert->expiry_date->format('d M, Y') }}</div>
                                        @else
                                            <span class="text-slate-400">No Expiry</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($cert->status === 'ISSUED')
                                            <span class="badge-success text-xs">ISSUED</span>
                                        @elseif($cert->status === 'PENDING_VERIFICATION')
                                            <span class="badge-warning text-xs">PENDING VERIFICATION</span>
                                        @elseif($cert->status === 'REJECTED')
                                            <span class="badge-danger text-xs">REJECTED</span>
                                        @else
                                            <span class="badge-neutral text-xs">DRAFT</span>
                                        @endif
                                        
                                        @if($cert->verifier_id)
                                            <div class="text-[11px] text-slate-400 mt-1">Verifier: {{ $cert->verifier->name ?? 'Unknown' }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            @if(request('filter') === 'to_verify' && $cert->status === 'PENDING_VERIFICATION')
                                                <a href="{{ route('engineer.certificates.verify', $cert->id) }}" class="btn-primary text-xs py-1 px-3 bg-amber-600 hover:bg-amber-700">
                                                    Verify Now
                                                </a>
                                            @endif

                                            @if($cert->status === 'ISSUED')
                                                <form action="{{ route('engineer.certificates.download', $cert->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="btn-ghost btn-sm text-emerald-600 dark:text-emerald-400 font-bold" title="Download PDF">
                                                        PDF
                                                    </button>
                                                </form>
                                            @endif

                                            @if(in_array($cert->status, ['DRAFT', 'REJECTED']) && $cert->created_by === auth()->id())
                                                <a href="{{ route('engineer.certificates.edit', $cert->id) }}" class="btn-secondary btn-sm">
                                                    Edit
                                                </a>
                                            @endif

                                            <a href="{{ route('engineer.certificates.show', $cert->id) }}" class="btn-ghost btn-sm text-brand-600 dark:text-brand-400">
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 text-sm">
                                        No certificates found matching filter criteria.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($certificates->hasPages())
                    <div class="mt-4 border-t border-slate-200 dark:border-slate-700/80 pt-4">
                        {{ $certificates->withQueryString()->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
