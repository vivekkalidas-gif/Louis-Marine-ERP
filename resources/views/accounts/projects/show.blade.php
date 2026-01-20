@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">{{ $project->name }}</h1>
             <div class="flex items-center gap-2 mt-1">
                <span class="px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:text-gray-300">{{ $project->code }}</span>
                <span class="text-gray-400">|</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">Manager: {{ $project->manager ? $project->manager->name : 'N/A' }}</span>
                <span class="text-gray-400">|</span>
                 <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                        {{ $project->status == 'completed' ? 'bg-green-100 text-green-800' : 
                           ($project->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800 dark:text-gray-200') }}">
                        {{ str_replace('_', ' ', ucfirst($project->status)) }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('accounts.projects.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 font-medium">Back to Projects</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
             <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Net Profit</div>
             <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold {{ $project->net_profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($project->net_profit, 2) }}
                </span>
             </div>
        </div>
        
         <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
             <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Budgeted Revenue</div>
             <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ number_format($project->budget_revenue, 2) }}
                </span>
             </div>
        </div>
        
          <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
             <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Budgeted Cost</div>
             <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ number_format($project->budget_cost, 2) }}
                </span>
             </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-900 dark:text-gray-100">Project Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Debits (Cost)</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Credits (Rev)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($project->journalItems()->with(['entry', 'account'])->latest()->limit(50)->get() as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->entry->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->entry->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->account->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">{{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">{{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No transactions recorded for this project yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
