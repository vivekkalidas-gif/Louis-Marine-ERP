@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
             <a href="{{ route('accounts.assets.index') }}" class="p-2 rounded-full hover:bg-gray-100 text-gray-500 dark:text-gray-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $asset->asset_code }} - {{ $asset->name }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $asset->category->name }} | {{ ucfirst($asset->status) }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Asset Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Asset Details</h3>
            <div class="space-y-4 text-sm">
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 dark:text-gray-400">Purchase Date</span>
                    <span class="font-medium">{{ $asset->purchase_date->format('M d, Y') }}</span>
                </div>
                 <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 dark:text-gray-400">Depreciation Start</span>
                    <span class="font-medium">{{ $asset->capitalization_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 dark:text-gray-400">Useful Life</span>
                    <span class="font-medium">{{ $asset->useful_life_years }} Years</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 dark:text-gray-400">Depreciation Method</span>
                    <span class="font-medium">{{ $asset->depreciation_method }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 dark:text-gray-400">Purchase Cost</span>
                    <span class="font-medium">{{ number_format($asset->purchase_cost, 2) }}</span>
                </div>
                 <div class="flex justify-between py-2 border-b border-gray-50">
                    <span class="text-gray-500 dark:text-gray-400">Salvage Value</span>
                    <span class="font-medium">{{ number_format($asset->salvage_value, 2) }}</span>
                </div>
            </div>
            
             <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-8 mb-4">Current Value</h3>
             <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                 <div class="flex justify-between mb-2">
                     <span class="text-gray-500 dark:text-gray-400">Accumulated Dep.</span>
                     <span class="font-medium text-red-600">-{{ number_format($asset->accumulated_depreciation, 2) }}</span>
                 </div>
                 <div class="flex justify-between pt-2 border-t border-gray-200">
                     <span class="text-gray-900 dark:text-gray-100 font-bold">Net Book Value</span>
                     <span class="text-gray-900 dark:text-gray-100 font-bold">{{ number_format($asset->current_value, 2) }}</span>
                 </div>
             </div>
        </div>

        <!-- Depreciation Schedule -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">Depreciation Schedule</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                             <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Opening NBV</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Depreciation</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Closing NBV</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Journal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($asset->schedules as $schedule)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $schedule->schedule_date->format('M Y') }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-500 dark:text-gray-400">{{ number_format($schedule->net_book_value_before, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100">{{ number_format($schedule->amount, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-gray-100">{{ number_format($schedule->net_book_value_after, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-center">
                                    @if($schedule->is_posted)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Posted</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-200">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                    @if($schedule->journalEntry)
                                        <a href="{{ route('accounts.journals.show', $schedule->journalEntry) }}" class="text-indigo-600 hover:underline">
                                            {{ $schedule->journalEntry->name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No schedule generated.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
