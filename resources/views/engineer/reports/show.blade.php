<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Service Report') }} - {{ $report->job->job_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Report Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">Service Report</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Report Date: {{ $report->report_date->format('d M, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                {{ $report->status }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Job: {{ $report->job->job_number }}</p>
                            @if($report->status !== 'Finalized') 
                            <a href="{{ route('engineer.reports.edit', $report->id) }}" class="inline-flex items-center mt-2 mr-2 px-3 py-1 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-900 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                Edit
                            </a>
                            @endif
                            <a href="{{ route('engineer.reports.download', $report->id) }}" target="_blank" class="inline-flex items-center mt-2 px-3 py-1 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-700/50">
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Customer Details</h4>
                        <p class="text-gray-900 dark:text-gray-200 font-medium">{{ $report->job->customer->company_name }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $report->job->customer->email }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Vessel / Site</h4>
                        <p class="text-gray-900 dark:text-gray-200 font-medium">{{ $report->job->vessel->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Services Section -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                Services Performed
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($report->items as $item)
                            <li class="px-4 py-4 sm:px-6 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                            {{ $item->serviceType->name }}
                                        </p>
                                    </div>
                                    <!-- Price removed -->
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Work Description -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100 mb-4">
                                Work Description
                            </h3>
                            <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                                <p>{{ $report->work_description }}</p>
                            </div>
                            
                            @if($report->recommendations)
                            <h4 class="text-md font-bold text-gray-900 dark:text-gray-100 mt-6 mb-2">Recommendations</h4>
                            <p class="text-gray-700 dark:text-gray-300">{{ $report->recommendations }}</p>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Sidebar: Charges & Totals -->
                <div class="space-y-6">
                    
                    <!-- Additional Charges -->
                    @if($report->charges->count() > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                         <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                Additional Charges
                            </h3>
                        </div>
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($report->charges as $charge)
                            <li class="px-4 py-3 sm:px-6 text-sm">
                                <div class="flex justify-between font-medium text-gray-900 dark:text-gray-200">
                                    <span>{{ $charge->item_description }}</span>
                                    <span>x {{ $charge->quantity }}</span>
                                </div>
                                <!-- Price breakdown removed -->
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Totals -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Summary</h3>
                            
                            <div class="flex justify-between items-center mb-2 text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Hours Worked:</span>
                                <span class="font-medium text-gray-900 dark:text-gray-200">{{ $report->hours_worked }} hrs</span>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

                            <!-- Grand Total removed -->
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
