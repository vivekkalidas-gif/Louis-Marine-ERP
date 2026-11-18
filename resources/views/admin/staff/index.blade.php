<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="mb-4 text-sm text-gray-500">Select a staff member to view their salary history and advance details.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($staff as $member)
                        <a href="{{ route('admin.staff.show', $member->id) }}" class="block p-6 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-600 transition border dark:border-gray-600">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center font-bold text-lg">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $member->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->employeeDetail->designation ?? $member->roles->pluck('name')->join(', ') }}</p>
                                    <p class="text-xs text-gray-400 mt-1">ID: {{ $member->employeeDetail->employee_code ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
