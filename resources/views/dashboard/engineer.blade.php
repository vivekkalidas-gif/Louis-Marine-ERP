<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Engineer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- KPIs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Assigned Jobs (Active)</div>
                    <div class="text-3xl font-bold text-marine-600 dark:text-marine-300">{{ $assignedJobs->count() }}</div>
                </div>
                <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Jobs Closed (This Month)</div>
                    <div class="text-3xl font-bold text-green-600 dark:text-green-300">{{ $recentClosed }}</div>
                </div>
            </div>

            <!-- Active Jobs List -->
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4 text-marine-800 dark:text-marine-200">My Queue</h3>
                    
                    @if($assignedJobs->count() > 0)
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($assignedJobs as $job)
                            <a href="{{ route('engineer.jobs.show', $job->id) }}" class="block p-4 border rounded hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-marine-800 transition border-l-4 
                                {{ $job->priority === 'Urgent' ? 'border-l-red-500' : ($job->priority === 'High' ? 'border-l-orange-500' : 'border-l-marine-500') }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-bold text-marine-900 dark:text-marine-100">{{ $job->title }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $job->vessel->name }} ({{ $job->vessel->type }})</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            SLA: {{ $job->sla_due_at ? $job->sla_due_at->format('M d') : 'None' }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="px-2 py-1 text-xs font-bold rounded bg-gray-100 dark:bg-marine-700 text-marine-800 dark:text-marine-200 mb-1">
                                            {{ $job->status }}
                                        </span>
                                        @if($job->priority === 'Urgent')
                                            <span class="text-xs font-bold text-red-600 animate-pulse">URGENT</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                            <svg class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <p>No active jobs assigned to you.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
