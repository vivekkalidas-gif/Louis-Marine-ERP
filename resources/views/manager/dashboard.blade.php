<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manager Overview') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- KPIs ROW 1: Operations -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Active Jobs -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Active Jobs</div>
                    <div class="mt-2 flex items-baseline">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $activeJobsCount }}</div>
                        <span class="ml-2 text-sm text-green-600 font-semibold">{{ $completedJobsThisMonth }} completed this month</span>
                    </div>
                </div>

                <!-- Engineers -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-teal-500">
                     <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Active Engineers</div>
                     <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $activeEngineers }}</div>
                </div>

                <!-- Pending Approvals -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                     <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Pending Approvals</div>
                     <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingApprovals }}</div>
                     <div class="mt-1 text-xs text-purple-600">Material Requests</div>
                </div>

                <!-- Expiring Certs -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                     <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Expiring Certs (30d)</div>
                     <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $expiringCertificates }}</div>
                </div>
                 <!-- Low Stock -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                     <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Low Stock Alerts</div>
                     <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $lowStockItems }}</div>
                </div>
            </div>

            <!-- KPIs ROW 2: Finance -->
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Pending Invoices</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $pendingInvoicesCount }}</div>
                    </div>
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                 <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 flex justify-between items-center">
                    <div>
                        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium uppercase">Revenue (This Month)</div>
                        <div class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">QAR {{ number_format($monthlyRevenue, 2) }}</div>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Revenue Trend (6 Months)</h3>
                    <div class="h-64">
                         <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Job Status Chart -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Job Status Distribution</h3>
                    <div class="h-64 flex justify-center">
                         <canvas id="jobStatusChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <a href="{{ route('admin.jobs.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                         <svg class="w-6 h-6 mb-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Job</span>
                    </a>
                    
                    <a href="{{ route('admin.users.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <svg class="w-6 h-6 mb-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Add User</span>
                    </a>

                    <a href="{{ route('accounts.invoices.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                         <svg class="w-6 h-6 mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                         <span class="text-sm font-medium text-gray-700 dark:text-gray-300">New Invoice</span>
                    </a>

                    <a href="{{ route('storekeeper.inventory.index') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        <svg class="w-6 h-6 mb-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Inventory</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Scripts -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueData['labels']),
                datasets: [{
                    label: 'Revenue (QAR)',
                    data: @json($revenueData['data']),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Job Status Chart
        const jobCtx = document.getElementById('jobStatusChart').getContext('2d');
        new Chart(jobCtx, {
            type: 'doughnut',
            data: {
                labels: @json($jobStatusData['labels']),
                datasets: [{
                    data: @json($jobStatusData['data']),
                    backgroundColor: [
                        'rgb(59, 130, 246)', // Blue
                        'rgb(34, 197, 94)', // Green
                        'rgb(234, 179, 8)', // Yellow
                        'rgb(239, 68, 68)', // Red
                        'rgb(107, 114, 128)' // Gray
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
    @endpush
</x-app-layout>
