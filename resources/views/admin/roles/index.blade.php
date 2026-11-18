<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-xl text-slate-900 dark:text-white leading-tight tracking-tight flex items-center gap-2">
                        <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Role & Permission Matrix</span>
                    </h2>
                    <span class="badge-neutral text-xs font-bold">Security Module</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Configure role-based access control, action privileges, and security matrix across ERP modules</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ activeRole: '{{ $roles->first()->id }}' }">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- ROLE SELECTOR PILLS HEADER -->
            <div class="theme-card p-3">
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider px-3 shrink-0">Select Role:</span>
                    @foreach($roles as $role)
                    <button 
                        @click="activeRole = '{{ $role->id }}'"
                        :class="activeRole === '{{ $role->id }}' ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/20' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:text-white hover:bg-slate-700/80'"
                        class="px-4 py-2 text-xs rounded-xl transition-all whitespace-nowrap flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>{{ $role->name }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- PERMISSION MATRIX FORMS BY ROLE -->
            @foreach($roles as $role)
            <div x-show="activeRole === '{{ $role->id }}'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform scale-98" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($permissions as $module => $perms)
                        <div class="theme-card module-group flex flex-col justify-between">
                            <div>
                                <!-- Module Header -->
                                <div class="flex justify-between items-center pb-3 mb-4 border-b border-slate-200 dark:border-slate-800">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-brand-50 dark:bg-brand-950/80 text-brand-600 dark:text-brand-400 border border-brand-200 dark:border-brand-800/50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        </div>
                                        <h4 class="font-bold text-sm text-slate-900 dark:text-white capitalize tracking-wide">{{ $module }}</h4>
                                    </div>
                                    <label class="inline-flex items-center gap-1.5 cursor-pointer bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] font-bold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors">
                                        <span>Select All</span>
                                        <input type="checkbox" 
                                            class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-brand-600 shadow-sm focus:ring-brand-500 select-all-toggle cursor-pointer"
                                            onclick="toggleModulePermissions(this)">
                                    </label>
                                </div>

                                <!-- Permission Items Checkboxes -->
                                <div class="space-y-2.5">
                                    @foreach($perms as $perm)
                                    <label class="flex items-center justify-between p-2 rounded-lg bg-slate-50/50 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-800/60 border border-slate-200/60 dark:border-slate-800/80 cursor-pointer transition-colors group">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ $perm->name }}</span>
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" 
                                            class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-brand-600 shadow-sm focus:ring-brand-500 permission-checkbox cursor-pointer"
                                            {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- FIXED BOTTOM ACTION BAR -->
                    <div class="sticky bottom-6 mt-8 flex justify-end">
                        <div class="theme-card p-3 border-2 border-brand-500/40 dark:border-brand-500/30 shadow-2xl backdrop-blur-md bg-white/90 dark:bg-slate-900/90">
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 font-bold gap-2 shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Save Permissions for {{ $role->name }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endforeach

        </div>
    </div>
    
    <script>
        function toggleModulePermissions(source) {
            const checkboxes = source.closest('.module-group').querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = source.checked;
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.module-group').forEach(group => {
                const checkboxes = group.querySelectorAll('.permission-checkbox');
                const selectAll = group.querySelector('.select-all-toggle');
                
                if (checkboxes.length > 0 && selectAll) {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    selectAll.checked = allChecked;

                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const allNowChecked = Array.from(checkboxes).every(c => c.checked);
                            selectAll.checked = allNowChecked;
                        });
                    });
                }
            });
        });
    </script>
</x-app-layout>
