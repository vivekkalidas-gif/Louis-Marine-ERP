<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 dark:text-white leading-tight tracking-tight">
            {{ __('Executive Manager Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            
            <!-- Operations Metric Grid (5 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="stat-card border-l-4 border-l-brand-500">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Jobs</p>
                    <div class="mt-1 flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $activeJobsCount }}</span>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold">+{{ $completedJobsThisMonth }} done</span>
                    </div>
                </div>

                <div class="stat-card border-l-4 border-l-teal-500">
                     <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Engineers</p>
                     <p class="mt-1 text-2xl font-black text-teal-600 dark:text-teal-400">{{ $activeEngineers }}</p>
                </div>

                <div class="stat-card border-l-4 border-l-purple-500">
                     <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Approvals</p>
                     <p class="mt-1 text-2xl font-black text-purple-600 dark:text-purple-400">{{ $pendingApprovals }}</p>
                </div>

                <div class="stat-card border-l-4 border-l-amber-500">
                     <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Expiring Certs (30d)</p>
                     <p class="mt-1 text-2xl font-black text-amber-600 dark:text-amber-400">{{ $expiringCertificates }}</p>
                </div>

                <div class="stat-card border-l-4 border-l-rose-500">
                     <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Low Stock Items</p>
                     <p class="mt-1 text-2xl font-black text-rose-600 dark:text-rose-400">{{ $lowStockItems }}</p>
                </div>
            </div>

            <!-- Finance Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Pending Accounts Invoices</p>
                        <p class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $pendingInvoicesCount }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                <div class="stat-card flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Monthly Revenue (QAR)</p>
                        <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">QAR {{ number_format($monthlyRevenue, 2) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/50">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Charts Section Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Chart Container -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">Revenue Trend (6 Months)</h3>
                    <div class="h-64">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Job Status Chart Container -->
                <div class="theme-card">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">Job Status Distribution</h3>
                    <div class="h-64 flex justify-center">
                        <canvas id="jobStatusChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Executive Actions Card -->
            <div class="theme-card">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 pb-2 border-b border-slate-200 dark:border-slate-700/80">Executive Shortcuts</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <a href="{{ route('admin.jobs.create') }}" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition flex flex-col items-center justify-center text-center group">
                        <svg class="w-6 h-6 mb-2 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">New Service Job</span>
                    </a>
                    
                    <a href="{{ route('admin.users.create') }}" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition flex flex-col items-center justify-center text-center group">
                        <svg class="w-6 h-6 mb-2 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Add Staff User</span>
                    </a>

                    <a href="{{ route('accounts.invoices.create') }}" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition flex flex-col items-center justify-center text-center group">
                        <svg class="w-6 h-6 mb-2 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Create Invoice</span>
                    </a>

                    <a href="{{ route('storekeeper.inventory.index') }}" class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700/80 bg-slate-50/50 dark:bg-slate-900/40 hover:bg-white dark:hover:bg-slate-800 transition flex flex-col items-center justify-center text-center group">
                        <svg class="w-6 h-6 mb-2 text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">Inventory Items</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Scripts -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.5)' : 'rgba(226, 232, 240, 0.8)';

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueData['labels']),
                datasets: [{
                    label: 'Revenue (QAR)',
                    data: @json($revenueData['data']),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                    tension: 0.35,
                    fill: true,
                    borderWidth: 2.5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: textColor, font: { weight: 'bold', size: 11 } } }
                },
                scales: {
                    x: { ticks: { color: textColor }, grid: { color: gridColor } },
                    y: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } }
                }
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
                        '#2563eb', // Blue
                        '#10b981', // Green
                        '#f59e0b', // Amber
                        '#f43f5e', // Rose
                        '#64748b'  // Slate
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: textColor, font: { weight: 'bold', size: 11 } } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
