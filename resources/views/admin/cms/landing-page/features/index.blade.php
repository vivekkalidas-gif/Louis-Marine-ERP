<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                    <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Features & Key Highlights</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Manage key capabilities, service guarantees, and highlights on landing page</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.cms.landing-page.features.create') }}" class="btn-primary text-xs font-bold gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Add Feature</span>
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

        <div class="theme-card">
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200 dark:border-slate-700/80 flex-wrap gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>Landing Page Features</span>
                        <span class="badge-neutral text-[10px]">{{ count($features) }} Features</span>
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Core value propositions on public homepage</p>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <tr>
                            <th class="p-3.5">Order</th>
                            <th class="p-3.5">Feature Title & Description</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                        @forelse($features as $feature)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition">
                                <td class="p-3.5 font-mono text-slate-500 dark:text-slate-400">#{{ $feature->sort_order }}</td>
                                <td class="p-3.5">
                                    <strong class="font-bold text-slate-900 dark:text-white block text-xs">{{ $feature->title }}</strong>
                                    <span class="text-[11px] text-slate-500 block max-w-md">{{ Str::limit($feature->description, 80) }}</span>
                                </td>
                                <td class="p-3.5">
                                    @if($feature->is_active)
                                        <span class="badge-emerald text-[10px] font-bold">Active</span>
                                    @else
                                        <span class="badge-rose text-[10px] font-bold">Inactive</span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.cms.landing-page.features.edit', $feature) }}" class="btn-secondary btn-sm text-xs gap-1 font-bold">
                                            <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('admin.cms.landing-page.features.destroy', $feature) }}" method="POST" onsubmit="return confirm('Delete?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-ghost btn-sm text-xs p-1.5 text-rose-500 hover:text-rose-700" title="Delete Feature">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center bg-slate-50/50 dark:bg-slate-900/40">
                                    <strong class="block text-sm font-bold text-slate-800 dark:text-slate-200">No Features Found</strong>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Get started by creating your first feature card.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
