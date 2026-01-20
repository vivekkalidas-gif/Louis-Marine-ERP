<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Log Details') }}
            </h2>
            <a href="{{ route('admin.activity-logs.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold py-2 px-4 rounded">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Log Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Log Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">User</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $log->user->name ?? 'System' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Timestamp</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $log->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Action</p>
                            <p>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $log->action === 'created' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Module</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $log->module }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Model</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $log->ip_address }}</p>
                        </div>
                        @if($log->user_agent)
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">User Agent</p>
                            <p class="text-gray-900 dark:text-gray-100 text-sm">{{ $log->user_agent }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Changes Comparison -->
            @if($log->action === 'updated' && $log->old_values && $log->new_values)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Changes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Field</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Old Value</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">New Value</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($log->new_values as $key => $newValue)
                                    @php
                                        $oldValue = $log->old_values[$key] ?? null;
                                        $hasChanged = $oldValue != $newValue;
                                    @endphp
                                    @if($hasChanged)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-red-600 dark:text-red-400">
                                            {{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?? '(empty)') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-green-600 dark:text-green-400">
                                            {{ is_array($newValue) ? json_encode($newValue) : ($newValue ?? '(empty)') }}
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @elseif($log->action === 'created' && $log->new_values)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Created Values</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($log->new_values as $key => $value)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ is_array($value) ? json_encode($value) : ($value ?? '(empty)') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @elseif($log->action === 'deleted' && $log->old_values)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Deleted Values</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($log->old_values as $key => $value)
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ is_array($value) ? json_encode($value) : ($value ?? '(empty)') }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
