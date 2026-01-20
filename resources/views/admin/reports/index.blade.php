<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
            {{ __('Reports & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Financial Statements Links -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Financial Statements</h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <a href="{{ route('accounts.reports.balance_sheet') }}" class="flex flex-col items-center justify-center p-4 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg border border-purple-200 dark:border-purple-700 transition-colors">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                         <span class="text-sm font-bold text-purple-900 dark:text-purple-100">Balance Sheet</span>
                    </a>
                    <a href="{{ route('accounts.reports.profit_loss') }}" class="flex flex-col items-center justify-center p-4 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg border border-green-200 dark:border-green-700 transition-colors">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                         <span class="text-sm font-bold text-green-900 dark:text-green-100">Profit & Loss</span>
                    </a>
                    <a href="{{ route('accounts.reports.cash_flow') }}" class="flex flex-col items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-700 transition-colors">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-sm font-bold text-blue-900 dark:text-blue-100">Cash Flow</span>
                    </a>
                    <a href="{{ route('accounts.reports.trial_balance') }}" class="flex flex-col items-center justify-center p-4 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 rounded-lg border border-orange-200 dark:border-orange-700 transition-colors">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path></svg>
                         <span class="text-sm font-bold text-orange-900 dark:text-orange-100">Trial Balance</span>
                    </a>
                    <a href="{{ route('accounts.reports.general_ledger') }}" class="flex flex-col items-center justify-center p-4 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-lg border border-indigo-200 dark:border-indigo-700 transition-colors">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                         <span class="text-sm font-bold text-indigo-900 dark:text-indigo-100">General Ledger</span>
                    </a>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" class="flex gap-4 items-end">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="shadow border rounded py-2 px-3">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="shadow border rounded py-2 px-3">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Filter
                    </button>
                    <div class="ml-auto flex gap-2">
                         <a href="{{ route('admin.finance.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold py-2 px-4 rounded">Finance Detail</a>
                         <a href="{{ route('admin.reports.tax') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold py-2 px-4 rounded">Tax (VAT)</a>
                         <a href="{{ route('admin.reports.operations') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-bold py-2 px-4 rounded">Ops Detail</a>
                    </div>
                </form>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase">Revenue (Selected Period)</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">QAR {{ number_format($totalRevenue, 2) }}</div>
                </div>
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase">Outstanding Dues</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">QAR {{ number_format($outstandingRevenue, 2) }}</div>
                </div>
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase">Active Jobs</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $activeJobs }}</div>
                </div>
                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-bold uppercase">Certs Issued</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $certsIssued }}</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Revenue Trend -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Revenue Trend (Last 6 Months)</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <!-- Job Status -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Job Status Distribution</h3>
                    <canvas id="jobChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Logic -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($revenueTrend)) !!},
                datasets: [{
                    label: 'Revenue (QAR)',
                    data: {!! json_encode(array_values($revenueTrend)) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: false
                }]
            }
        });

        // Job Chart
        const jobCtx = document.getElementById('jobChart');
        const jobStats = {!! json_encode($jobStats) !!};
        new Chart(jobCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(jobStats),
                datasets: [{
                    label: 'Jobs',
                    data: Object.values(jobStats),
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)'
                    ],
                    hoverOffset: 4
                }]
            }
        });
    </script>
</x-app-layout>
