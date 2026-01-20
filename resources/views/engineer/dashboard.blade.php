<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Engineer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Calibration Warning -->
            @if(isset($expiringEquipmentCount) && $expiringEquipmentCount > 0)
            <div class="mb-8 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-lg p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-full text-orange-600 dark:text-orange-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Equipment Calibration Due</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            You have <span class="font-semibold text-orange-600 dark:text-orange-400">{{ $expiringEquipmentCount }}</span> item(s) requiring calibration within the next 30 days.
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.dispatch.create', ['equipment_ids' => $expiringEquipmentIds, 'type' => 'Calibration']) }}" class="whitespace-nowrap px-4 py-2 bg-white text-orange-600 border border-orange-200 hover:bg-orange-50 text-sm font-medium rounded-md shadow-sm transition">
                        Prepare Dispatch
                    </a>
                    <a href="{{ route('engineer.equipment.index', ['status' => 'expiring_soon']) }}" class="whitespace-nowrap px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-md shadow-sm transition">
                        View Equipment
                    </a>
                </div>
            </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- My Jobs -->
                <a href="{{ route('engineer.jobs.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-500 bg-opacity-20 text-blue-600 dark:text-blue-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">My Jobs</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $myJobsCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- In Progress -->
                <a href="{{ route('engineer.jobs.index', ['status' => 'in-progress']) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-500 bg-opacity-20 text-yellow-600 dark:text-yellow-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">In Progress</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $inProgressCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Completed -->
                <a href="{{ route('engineer.jobs.index', ['status' => 'completed']) }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-20 text-green-600 dark:text-green-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Completed</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completedCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Total Customers -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-20 text-purple-600 dark:text-purple-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Customers</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customersCount ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('engineer.jobs.create') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 group">
                    <div class="p-6 flex items-center">
                        <div class="p-3 rounded-full bg-indigo-500 bg-opacity-20 text-indigo-600 dark:text-indigo-400 group-hover:bg-opacity-30 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">New Job</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Create service job</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.customers.create') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 group">
                    <div class="p-6 flex items-center">
                        <div class="p-3 rounded-full bg-green-500 bg-opacity-20 text-green-600 dark:text-green-400 group-hover:bg-opacity-30 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">New Customer</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Add customer company</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.equipment.categories.index') }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200 group">
                    <div class="p-6 flex items-center">
                        <div class="p-3 rounded-full bg-teal-500 bg-opacity-20 text-teal-600 dark:text-teal-400 group-hover:bg-opacity-30 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Equipment</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage master list</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Recent Jobs Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Jobs / Queue</h3>
                        <a href="{{ route('engineer.jobs.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>

                    @if(isset($recentJobs) && $recentJobs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Job #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vessel</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($recentJobs as $job)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $job->job_number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $job->customer->company_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $job->vessel->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($job->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($job->status === 'in-progress') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($job->status === 'requested') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @else bg-gray-100 text-gray-800 dark:text-gray-200 dark:bg-gray-700 dark:text-gray-300
                                            @endif">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $job->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('engineer.jobs.show', $job->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 dark:hover:text-blue-300">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No jobs yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new service job.</p>
                        <div class="mt-6">
                            <a href="{{ route('engineer.jobs.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                New Job
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
