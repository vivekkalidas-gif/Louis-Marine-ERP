<x-app-layout>
    <x-slot name="header">
        <h2 class="theme-text-header">
            {{ __('Assign Technicians') }} - {{ $job->job_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="theme-card">
                <!-- Job Summary -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-marine-800 rounded">
                    <h3 class="font-bold text-marine-900 dark:text-marine-100 mb-2">Job Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Customer:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $job->customer->company_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Vessel:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $job->vessel->name ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $job->status }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Scheduled:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $job->scheduled_at ? $job->scheduled_at->format('d M Y, H:i') : 'Not Scheduled' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Current Assignments -->
                @if($currentAssignments->count() > 0)
                <div class="mb-6">
                    <h3 class="font-bold text-marine-900 dark:text-marine-100 mb-3">Current Assignments</h3>
                    <div class="space-y-2">
                        @foreach($currentAssignments as $assignment)
                        <div class="flex items-center p-3 bg-blue-50 dark:bg-blue-900 rounded">
                            <div class="w-8 h-8 bg-marine-200 dark:bg-marine-600 rounded-full flex items-center justify-center text-marine-800 dark:text-marine-100 font-bold text-xs mr-3">
                                {{ substr($assignment->user->name, 0, 2) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $assignment->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment->user->email }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Assignment Form -->
                <form method="POST" action="{{ route('engineer.assignments.store', $job->id) }}">
                    @csrf

                    <div class="mb-6">
                        <label class="theme-label">Select Technicians *</label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Choose one or more technicians to assign to this job</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($technicians as $tech)
                            <label class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-marine-800 border dark:border-marine-700 rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-marine-700 transition">
                                <input type="checkbox" name="technicians[]" value="{{ $tech->id }}" 
                                       class="rounded text-marine-600 focus:ring-marine-500"
                                       {{ $currentAssignments->contains('user_id', $tech->id) ? 'checked' : '' }}>
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-marine-200 dark:bg-marine-600 rounded-full flex items-center justify-center text-marine-800 dark:text-marine-100 font-bold text-xs">
                                        {{ substr($tech->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $tech->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tech->email }}</p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>

                        @error('technicians')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('engineer.jobs.show', $job->id) }}" class="bg-gray-50 dark:bg-gray-7000 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                            Cancel
                        </a>
                        <button type="submit" class="bg-marine-700 hover:bg-marine-600 text-white font-bold py-2 px-6 rounded">
                            Assign Technicians
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
