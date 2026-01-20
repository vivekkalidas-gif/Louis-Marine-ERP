@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $budget->name }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $budget->fiscal_year }} | {{ ucfirst($budget->period_type) }}</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('accounts.budgets.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 font-medium mr-2">Back</a>
             <!-- <button class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">Export PDF</button> -->
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('accounts.budgets.items.update', $budget) }}" method="POST">
        @csrf
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-gray-200">Budget vs Actuals</h3>
                <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Save Changes</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase">Budgeted</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase">Actual</th>
                            <th class="px-6 py-3 text-right font-medium text-gray-500 dark:text-gray-400 uppercase">Variance</th>
                            <th class="px-6 py-3 text-center font-medium text-gray-500 dark:text-gray-400 uppercase">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php $currentType = ''; @endphp
                        
                        @foreach($report as $line)
                            @if($currentType != $line['account']->type)
                                @php $currentType = $line['account']->type; @endphp
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <td colspan="5" class="px-6 py-2 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                                        {{ str_replace('_', ' ', ucfirst($currentType)) }}
                                    </td>
                                </tr>
                            @endif
                        
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 group">
                                <td class="px-6 py-3 text-gray-900 dark:text-gray-100 font-medium">
                                    {{ $line['account']->code }} - {{ $line['account']->name }}
                                    <input type="hidden" name="items[{{ $loop->index }}][account_id]" value="{{ $line['account']->id }}">
                                </td>
                                <td class="px-6 py-3 text-right text-gray-500 dark:text-gray-400">
                                    <input type="number" step="0.01" name="items[{{ $loop->index }}][amount]" value="{{ number_format($line['budgeted'], 2, '.', '') }}" class="w-32 text-right text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-3 text-right text-gray-900 dark:text-gray-100">
                                    {{ number_format($line['actual'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-right font-medium {{ $line['variance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($line['variance'], 2) }}
                                </td>
                                <td class="px-6 py-3 text-center text-xs">
                                    @if($line['variance_percent'] != 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full {{ $line['variance'] < 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ round($line['variance_percent']) }}%
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
             <div class="p-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-100 flex justify-end">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition-colors">
                    Save Budget
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
