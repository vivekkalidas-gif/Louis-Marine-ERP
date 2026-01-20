<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('PRO Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Pending Certificates -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Pending Approvals</h3>
                    @if($pendingCertificates->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer / Vessel</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                                    @foreach($pendingCertificates as $cert)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                {{ $cert->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $cert->type->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">{{ $cert->customer->name }}</div>
                                                <div>{{ $cert->vessel->name ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.certificates.show', $cert->id) }}" class="text-indigo-600 hover:text-indigo-900">Review</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No certificates pending approval.</p>
                    @endif
                </div>
            </div>

            <!-- Recent History -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Recent Activity</h3>
                    @if($recentHistory->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($recentHistory as $cert)
                                <li class="py-4 flex justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cert->certificate_number }} ({{ $cert->type->name }})</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $cert->outcome == 'ISSUED' ? 'Approved' : 'Rejected' }} on {{ $cert->updated_at->format('M d, H:i') }}</div>
                                    </div>
                                    <div>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $cert->status == 'ISSUED' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $cert->status }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No recent activity.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
