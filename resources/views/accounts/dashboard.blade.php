@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header with Date Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Financial Dashboard</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview of your business financial health.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500 dark:text-gray-400">Period:</span>
            <div class="flex bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-1">
                <a href="{{ route('accounts.dashboard', ['range' => 'today']) }}" class="px-3 py-1 text-xs font-medium rounded-md {{ $range == 'today' ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700' }}">Today</a>
                <a href="{{ route('accounts.dashboard', ['range' => 'month']) }}" class="px-3 py-1 text-xs font-medium rounded-md {{ $range == 'month' ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700' }}">This Month</a>
                <a href="{{ route('accounts.dashboard', ['range' => 'quarter']) }}" class="px-3 py-1 text-xs font-medium rounded-md {{ $range == 'quarter' ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700' }}">Quarter</a>
                <a href="{{ route('accounts.dashboard', ['range' => 'year']) }}" class="px-3 py-1 text-xs font-medium rounded-md {{ $range == 'year' ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-700' }}">Year</a>
            </div>
        </div>
    </div>

    <!-- Notifications -->
    @if(isset($completedJobsPendingInvoice) && $completedJobsPendingInvoice->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="ml-3 w-full">
                <h3 class="text-sm leading-5 font-bold text-yellow-800">
                    Attention: {{ $completedJobsPendingInvoice->count() }} Completed Jobs Ready for Invoicing
                </h3>
                <div class="mt-2 text-sm leading-5 text-yellow-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($completedJobsPendingInvoice->take(6) as $job)
                            <div class="bg-white dark:bg-gray-800 p-2 rounded border border-yellow-200 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold">{{ $job->job_number }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $job->customer->name ?? '-' }}</span>
                                </div>
                                <a href="{{ route('accounts.invoices.create', ['job_id' => $job->id]) }}" class="text-xs bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700">
                                    Generate Invoice
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Pending Delivery Notes Alert -->
    @if(isset($pendingDeliveryNotes) && $pendingDeliveryNotes->count() > 0)
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-md shadow-sm mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="ml-3 w-full">
                <h3 class="text-sm leading-5 font-bold text-blue-800">
                    Attention: {{ $pendingDeliveryNotes->count() }} Dispatched Delivery Notes Pending Invoice
                </h3>
                <div class="mt-2 text-sm leading-5 text-blue-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($pendingDeliveryNotes->take(6) as $note)
                            <div class="bg-white dark:bg-gray-800 p-2 rounded border border-blue-200 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold">{{ $note->dr_number }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $note->customer->company_name ?? '-' }}</span>
                                </div>
                                <form action="{{ route('storekeeper.delivery-notes.create-invoice', $note->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs bg-indigo-600 text-white px-2 py-1 rounded hover:bg-indigo-700">
                                        Generate Invoice
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Net Profit -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Profit</h3>
                <span class="p-2 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($netProfit, 2) }}</span>
                @if($range == 'month')
                    <!-- Placeholder for trend -->
                    <!-- <span class="text-xs font-medium text-green-600">+12%</span> -->
                @endif
            </div>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Rev: {{ number_format($income, 2) }} | Exp: {{ number_format($expense, 2) }}</p>
        </div>

        <!-- Liquidity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cash & Bank</h3>
                 <span class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalLiquidity, 2) }}</span>
            </div>
             <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Total Available Liquidity</p>
        </div>

        <!-- Receivables -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Receivables (AR)</h3>
                 <span class="p-2 rounded-lg bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalReceivable, 2) }}</span>
            </div>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $overdueInvoices }} Overdue Invoices</p>
        </div>

        <!-- Payables -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payables (AP)</h3>
                 <span class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalPayable, 2) }}</span>
            </div>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Outstanding Vendor Bills</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Revenue vs Expense -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
             <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Financial Performance</h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Last 6 Months</span>
            </div>
            <div class="h-80">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity / Alerts -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
             <nav class="space-y-2">
                <a href="{{ route('accounts.invoices.create') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Create Invoice</span>
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                 <a href="{{ route('accounts.vendor-bills.create') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Record Vendor Bill</span>
                     <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('accounts.journals.create') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Manual Journal Entry</span>
                     <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                 <a href="{{ route('accounts.cash-bank-book.index') }}" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">View Cash Book</span>
                     <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </nav>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        const labels = @json($labels);
        const revenueData = @json($revenueData);
        const expenseData = @json($expenseData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenueData,
                        backgroundColor: 'rgba(79, 70, 229, 0.8)', // Indigo
                        borderRadius: 4,
                    },
                    {
                        label: 'Expenses',
                        data: expenseData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)', // Red
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            borderDash: [2, 2]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
