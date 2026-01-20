<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Role & Permission Matrix') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Manage what each role can do. <span class="font-bold">Super Admin</span> has full access by default and is hidden.
                </p>

                <div x-data="{ activeRole: '{{ $roles->first()->id }}' }">
                    <!-- Role Tabs -->
                    <div class="flex space-x-2 border-b border-gray-200 dark:border-gray-700 mb-6 overflow-x-auto">
                        @foreach($roles as $role)
                        <button 
                            @click="activeRole = '{{ $role->id }}'"
                            :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400 font-bold': activeRole === '{{ $role->id }}', 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-300': activeRole !== '{{ $role->id }}' }"
                            class="px-4 py-2 border-b-2 transition-colors whitespace-nowrap">
                            {{ $role->name }}
                        </button>
                        @endforeach
                    </div>

                    <!-- Matrix Forms -->
                    @foreach($roles as $role)
                    <div x-show="activeRole === '{{ $role->id }}'" style="display: none;">
                        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($permissions as $module => $perms)
                                <div class="border border-gray-200 dark:border-gray-700 rounded p-4 bg-gray-50 dark:bg-gray-700 module-group">
                                    <div class="flex justify-between items-center mb-2 border-b border-gray-300 dark:border-gray-600 pb-1">
                                        <h4 class="font-bold text-gray-800 dark:text-white capitalize">{{ $module }}</h4>
                                        <label class="inline-flex items-center">
                                            <span class="text-xs text-gray-600 dark:text-gray-400 mr-2">Select All</span>
                                            <input type="checkbox" 
                                                class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 select-all-toggle"
                                                onclick="toggleModulePermissions(this)">
                                        </label>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($perms as $perm)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" 
                                                class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 permission-checkbox"
                                                {{ $role->permissions->contains($perm->id) ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $perm->name }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                                    Save Permissions for {{ $role->name }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>

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
            // Initialize Select All checkboxes state
            document.querySelectorAll('.module-group').forEach(group => {
                const checkboxes = group.querySelectorAll('.permission-checkbox');
                const selectAll = group.querySelector('.select-all-toggle');
                
                if (checkboxes.length > 0 && selectAll) {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    selectAll.checked = allChecked;

                    // Add change listener to individual checkboxes to update Select All state
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
