@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Depreciation Run</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review and post monthly depreciation entries.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('accounts.assets.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 font-medium">Back to Assets</a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Pending Depreciation (Up to {{ now()->endOfMonth()->format('M Y') }})</h3>
            
            @if($pending->count() > 0)
                <form action="{{ route('accounts.assets.depreciation.post') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure you want to post depreciation for all listed items? This will create Journal Entries.')" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-sm transition-colors">
                        Post All Entries
                    </button>
                </form>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Schedule Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Depreciation Amount</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Resulting NBV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($pending as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $item->schedule_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                <a href="{{ route('accounts.assets.show', $item->asset) }}" class="hover:underline">{{ $item->asset->name }}</a>
                                <span class="text-gray-400 text-xs ml-1">{{ $item->asset->asset_code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->asset->category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900 dark:text-gray-100">{{ number_format($item->amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">{{ number_format($item->net_book_value_after, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">All caught up!</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No pending depreciation for the current period.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($pending->count() > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-gray-100">Total</td>
                            <td class="px-6 py-3 text-right text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($pending->sum('amount'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
