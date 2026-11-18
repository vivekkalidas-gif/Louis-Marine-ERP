<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
                    {{ __('Request Tool Allocation') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Browse available workshop tools and equipment by category</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('engineer.tools.index') }}" class="btn-ghost text-xs gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Back to Locker</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="max-w-7xl mx-auto space-y-6">

            <!-- Search & Filter Card -->
            <div class="theme-card">
                <form action="{{ route('engineer.tools.create') }}" method="GET" class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 absolute left-3.5 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="theme-input text-xs pl-10 py-2.5 font-medium" 
                               placeholder="Search tool by name, brand, code, or model...">
                    </div>
                    <button type="submit" class="btn-primary text-xs font-bold py-2.5 px-5 gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Search</span>
                    </button>
                    @if(request('search'))
                    <a href="{{ route('engineer.tools.create') }}" class="btn-secondary text-xs py-2.5">Clear</a>
                    @endif
                </form>
            </div>

            <!-- Grouped Available Tools -->
            @forelse($groupedTools as $type => $tools)
            <div class="theme-card space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-700/80">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand-500"></span>
                        <span>{{ $type }}</span>
                    </h3>
                    <span class="badge-neutral text-[10px]">{{ $tools->count() }} Available</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-700/80">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="p-3.5 min-w-[200px]">Tool Name & Serial Code</th>
                                <th class="p-3.5 min-w-[160px]">Brand / Model</th>
                                <th class="p-3.5 min-w-[220px]">Notes for Request</th>
                                <th class="p-3.5 w-32 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                            @foreach($tools as $tool)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white text-xs">{{ $tool->name }}</div>
                                    <span class="font-mono text-[10px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded mt-0.5 inline-block">{{ $tool->code }}</span>
                                </td>

                                <td class="p-3.5">
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $tool->brand ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $tool->model ?? '' }}</div>
                                </td>

                                <td class="p-3.5">
                                    <form id="request-form-{{ $tool->id }}" action="{{ route('engineer.tools.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="equipment_item_id" value="{{ $tool->id }}">
                                        <input type="text" name="notes" class="theme-input text-xs py-1.5 font-medium" placeholder="e.g. For Job Order #104 / Vessel Inspection...">
                                    </form>
                                </td>

                                <td class="p-3.5 text-right">
                                    <button form="request-form-{{ $tool->id }}" type="submit" 
                                            class="btn-primary btn-sm text-xs py-1.5 px-4 font-bold shadow-sm inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Request</span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @empty
            <div class="theme-card py-12 text-center text-slate-400 dark:text-slate-500">
                <svg class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">No available tools found</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try adjusting your search query or contact storekeeper.</p>
            </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
