<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Partners & Key Clients</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage partner logos, corporate clients, and public website brand endorsements</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cms.partners.create') }}" class="btn-primary text-xs font-bold gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Add New Partner</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/80 text-emerald-800 dark:text-emerald-200 text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <!-- Partners Table Card -->
        <div class="theme-card">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200 dark:border-slate-700/80 flex-wrap gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Corporate Partners</span>
                        <span class="badge-neutral text-[10px]">{{ count($partners) }} Brands</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Logos and brand links displayed on website homepage & footer</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <tr>
                            <th class="p-3.5">Partner Name & Website</th>
                            <th class="p-3.5">Brand Logo</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                        @forelse($partners as $partner)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3.5">
                                    <strong class="font-bold text-slate-900 dark:text-white block text-xs">{{ $partner->name }}</strong>
                                    @if($partner->website_url)
                                        <a href="{{ $partner->website_url }}" target="_blank" class="text-[11px] text-brand-600 dark:text-brand-400 hover:underline font-mono">{{ $partner->website_url }}</a>
                                    @else
                                        <span class="text-[11px] text-slate-400">No website provided</span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    @if($partner->logo_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($partner->logo_path) }}" class="h-9 w-auto max-w-[120px] object-contain p-1 rounded bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                    @else
                                        <span class="text-slate-400 text-xs italic">No Logo Uploaded</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.cms.partners.edit', $partner) }}" class="btn-secondary btn-sm text-xs gap-1 font-bold">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('admin.cms.partners.destroy', $partner) }}" method="POST" onsubmit="return confirm('Are you sure?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost btn-sm text-xs p-1.5 text-rose-500 hover:text-rose-700" title="Delete Partner">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center bg-slate-50/50 dark:bg-slate-900/40">
                                    <strong class="block text-sm font-bold text-slate-800 dark:text-slate-200">No Partners Found</strong>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Get started by adding your first corporate partner logo.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
