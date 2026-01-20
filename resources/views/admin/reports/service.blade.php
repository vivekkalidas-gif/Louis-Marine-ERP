<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Service Operations Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Job Status Chart -->
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Job Status Distribution</h3>
                <div class="relative h-64 w-full">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Engineer Leaderboard -->
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4">Engineer Performance</h3>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Engineer</th>
                            <th class="text-right py-2">Jobs Completed</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($engineers as $eng)
                            <tr class="border-b last:border-0 hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700">
                                <td class="py-3">{{ $eng->name }}</td>
                                <td class="py-3 text-right font-bold text-marine-600">{{ $eng->jobs_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxStatus = document.getElementById('statusChart');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($jobStatus)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($jobStatus)) !!},
                    backgroundColor: ['#FCA5A5', '#FCD34D', '#93C5FD', '#10B981', '#6B7280'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    </script>
</x-app-layout>
