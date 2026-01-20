@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create New Budget</h1>
        <a href="{{ route('accounts.budgets.index') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 font-medium">Cancel</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('accounts.budgets.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Master Budget 2026" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fiscal Year <span class="text-red-500">*</span></label>
                    <select name="fiscal_year" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @for($i = date('Y') - 1; $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Budget Period <span class="text-red-500">*</span></label>
                    <select name="period_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="yearly">Yearly (Simple)</option>
                        <!-- <option value="monthly">Monthly (Detailed) - Coming Soon</option> -->
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('accounts.budgets.index') }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700">Create & Plan</button>
            </div>
        </form>
    </div>
</div>
@endsection
