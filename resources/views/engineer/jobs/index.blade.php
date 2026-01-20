<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
                {{ __('All Jobs') }}
            </h2>
            <a href="{{ route('engineer.jobs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">Create New Job</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex space-x-4">
                    <a href="{{ route('engineer.jobs.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ !request('status') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300' }}">
                        All Jobs
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'requested']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') == 'requested' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300' }}">
                        Requested
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'in-progress']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') == 'in-progress' ? 'bg-indigo-100 text-indigo-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300' }}">
                        In Progress
                    </a>
                    <a href="{{ route('engineer.jobs.index', ['status' => 'completed']) }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request('status') == 'completed' ? 'bg-green-100 text-green-700' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300' }}">
                        Completed
                    </a>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Job Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Assigned Technician</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($jobs as $job)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $job->job_number }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 capitalize">{{ $job->job_location ?? 'Field' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $job->engineer->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $job->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('engineer.jobs.show', $job->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    
                                    @can('update', $job)
                                        <a href="{{ route('engineer.assignments.create', $job->id) }}" class="text-teal-600 hover:text-teal-900">Assign Tech</a>
                                        {{-- Add Edit Job link when edit functionality is implemented --}}
                                        {{-- <a href="{{ route('engineer.jobs.edit', $job->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a> --}}
                                    @else
                                        <span class="text-gray-400 text-xs">(View Only)</span>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No jobs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-4">
                        {{ $jobs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
