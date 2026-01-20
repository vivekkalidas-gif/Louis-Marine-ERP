<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-dashboard.quick-action title="Add User" url="#" icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>' />
                <x-dashboard.quick-action title="New Job" url="#" icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>' />
                <x-dashboard.quick-action title="Reports" url="#" icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' />
                <x-dashboard.quick-action title="Settings" url="#" icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>' color="gray" />
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($stats as $stat)
                    <x-dashboard.stat-card 
                        :title="$stat['title']" 
                        :value="$stat['value']" 
                        :icon="$stat['icon']" 
                        :trend="$stat['trend'] ?? null"
                        :color="$stat['color'] ?? 'marine'" 
                    />
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Chart Area -->
                <div class="lg:col-span-2 space-y-6">
                    <x-dashboard.chart-card title="Revenue Overview" canvasId="revenueChart" />
                    
                    <!-- Expiry Alerts -->
                    <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700 font-bold text-marine-900 dark:text-marine-100 flex justify-between items-center">
                            <span class="text-red-600">Expiry Alerts</span>
                        </div>
                        <div class="p-6">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-marine-800 dark:text-gray-400">
                                    <tr>
                                        <th class="px-6 py-3">Item</th>
                                        <th class="px-6 py-3">Due Date</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expiring as $item)
                                        <tr class="bg-white dark:bg-gray-800 border-b dark:bg-marine-900 dark:border-marine-800">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item['title'] }}</td>
                                            <td class="px-6 py-4">{{ $item['date'] }}</td>
                                            <td class="px-6 py-4"><span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $item['days'] }} days left</span></td>
                                            <td class="px-6 py-4"><button class="text-marine-600 hover:underline">Renew</button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Side Panel (Notifications & Extra Charts) -->
                <div class="space-y-6">
                    <x-dashboard.notification-panel :items="$notifications" />
                    <x-dashboard.chart-card title="Job Status Distribution" canvasId="jobChart" />
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue (QAR)',
                        data: [12000, 19000, 3000, 5000, 2000, 30000],
                        borderColor: '#0284c7', // marine-600
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            const jobCtx = document.getElementById('jobChart').getContext('2d');
            new Chart(jobCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'Pending', 'In Progress'],
                    datasets: [{
                        data: [65, 20, 15],
                        backgroundColor: ['#0284c7', '#fbbf24', '#38bdf8']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        });
    </script>
</x-app-layout>
