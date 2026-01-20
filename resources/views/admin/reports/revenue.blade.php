<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-marine-900 dark:text-marine-100 leading-tight">
            {{ __('Revenue Report') }} - {{ $year }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-marine-900 overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- Chart Container -->
                <div class="relative h-96 w-full mb-8">
                    <canvas id="revenueChart"></canvas>
                </div>

                <!-- Data Table -->
                <h3 class="font-bold text-lg mb-4">Monthly Breakdown</h3>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-marine-800">
                    <thead class="bg-gray-50 dark:bg-marine-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Revenue (QAR)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-marine-900 divide-y divide-gray-200 dark:divide-marine-800">
                        @php
                            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            $totalYear = 0;
                        @endphp
                        @foreach($data as $index => $amount)
                            @php $totalYear += $amount; @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $months[$index] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">{{ number_format($amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 dark:bg-marine-800 font-bold">
                        <tr>
                            <td class="px-6 py-4">TOTAL</td>
                            <td class="px-6 py-4 text-right">{{ number_format($totalYear, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue (QAR)',
                    data: {{ json_encode($data) }},
                    backgroundColor: '#10B981',
                    borderRadius: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</x-app-layout>
