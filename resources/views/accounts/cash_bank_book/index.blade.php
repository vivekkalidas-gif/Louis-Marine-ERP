@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Cash & Bank Book</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Real-time view of your liquidity accounts.</p>
        </div>
        <div class="flex items-center gap-3">
             <!-- Actions (e.g. New Transfer) could go here -->
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($accounts as $account)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-2 rounded-lg 
                        {{ $account->type == 'cash' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                        @if($account->type == 'cash')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                        @endif
                    </div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded">
                        {{ ucfirst($account->type) }}
                    </span>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $account->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $account->code }}</p>
                
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($account->current_balance, 2) }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Current Balance</span>
                </div>
                
                <a href="{{ route('accounts.cash-bank-book.show', $account) }}" class="block w-full py-2 px-4 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium text-center rounded-lg border border-gray-200 dark:border-gray-600 transition-colors">
                    View Ledger
                </a>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-700 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Cash or Bank Accounts Found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ensure you have accounts with type 'Cash', 'Bank', or 'Liquidity' in your Chart of Accounts.</p>
                <div class="mt-6">
                    <a href="{{ route('accounts.coa.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-medium">
                        Go to Chart of Accounts &rarr;
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
