<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">User</label>
                        <select name="user_id" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Module</label>
                        <select name="module" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                    {{ $module }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Action</label>
                        <select name="action" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                            <option value="">All Actions</option>
                            <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                            <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                            <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    </div>
                    
                    <div class="md:col-span-5 flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold py-2 px-4 rounded">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Activity Logs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Timestamp</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Action</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Module</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">IP Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($logs as $log)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->user->name ?? 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $log->action === 'created' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $log->action === 'deleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                            {{ ucfirst($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $log->module }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ class_basename($log->model_type) }} #{{ $log->model_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $log->ip_address }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        No activity logs found.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
