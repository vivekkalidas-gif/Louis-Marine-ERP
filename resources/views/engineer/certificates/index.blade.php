@extends('layouts.app')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Certificates</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and verify equipment certificates</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('engineer.certificates.create') }}" class="inline-flex items-center px-4 py-2 bg-marine-600 text-white rounded-lg hover:bg-marine-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New Certificate
            </a>
        </div>
    </div>

    <!-- Tears / Filter -->
    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-100 flex space-x-1">
        <a href="{{ route('engineer.certificates.index') }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ !request('filter') ? 'bg-white dark:bg-gray-800 text-marine-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white dark:bg-gray-800 hover:text-gray-900 dark:text-gray-100' }}">
            My Certificates
        </a>
        <a href="{{ route('engineer.certificates.index', ['filter' => 'to_verify']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center {{ request('filter') === 'to_verify' ? 'bg-white dark:bg-gray-800 text-marine-700 shadow-sm ring-1 ring-gray-200' : 'text-gray-600 hover:bg-white dark:bg-gray-800 hover:text-gray-900 dark:text-gray-100' }}">
            To Verify
            @if(isset($pendingCount) && $pendingCount > 0)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">{{ $pendingCount }}</span>
            @endif
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 text-gray-600 text-xs uppercase tracking-wider">
                    <th class="px-6 py-3 font-medium">Certificate No</th>
                    <th class="px-6 py-3 font-medium">Type</th>
                    <th class="px-6 py-3 font-medium">Customer / Vessel</th>
                    <th class="px-6 py-3 font-medium">Dates</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($certificates as $cert)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $cert->certificate_number ?? 'DRAFT' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Created by: {{ $cert->creator->name ?? 'Unknown' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $cert->certificateTemplate->name ?? ($cert->type->name ?? 'N/A') }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cert->equipmentCategory->name ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $cert->customer->company_name ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $cert->vessel->name ?? 'Unknown' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($cert->issue_date)
                            <div class="text-xs text-gray-900 dark:text-gray-100">Issued: {{ $cert->issue_date->format('d M, Y') }}</div>
                        @endif
                        @if($cert->expiry_date)
                            <div class="text-xs text-gray-500 dark:text-gray-400">Expires: {{ $cert->expiry_date->format('d M, Y') }}</div>
                        @else
                            <span class="text-xs text-gray-400">No Expiry</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusClasses = [
                                'DRAFT' => 'bg-gray-100 text-gray-800 dark:text-gray-200',
                                'PENDING_VERIFICATION' => 'bg-yellow-100 text-yellow-800',
                                'ISSUED' => 'bg-green-100 text-green-800',
                                'REJECTED' => 'bg-red-100 text-red-800',
                                'EXPIRED' => 'bg-orange-100 text-orange-800',
                            ];
                            $statusClass = $statusClasses[$cert->status] ?? 'bg-gray-100 text-gray-800 dark:text-gray-200';
                            $statusLabel = str_replace('_', ' ', $cert->status);
                        @endphp
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                        @if($cert->verifier_id)
                            <div class="text-xs text-gray-400 mt-1">Verifier: {{ $cert->verifier->name ?? 'Unknown' }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        @if(request('filter') === 'to_verify' && $cert->status === 'PENDING_VERIFICATION')
                            <a href="{{ route('engineer.certificates.verify', $cert->id) }}" class="inline-flex items-center px-3 py-1.5 bg-marine-600 text-white text-xs font-medium rounded hover:bg-marine-700">
                                Verify
                            </a>
                        @endif

                        @if($cert->status === 'ISSUED')
                            <form action="{{ route('engineer.certificates.download', $cert->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-marine-600" title="Download PDF">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </button>
                            </form>
                        @endif

                        @if(in_array($cert->status, ['DRAFT', 'REJECTED']) && $cert->created_by === auth()->id())
                            <a href="{{ route('engineer.certificates.edit', $cert->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit Certificate">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        @endif

                        <a href="{{ route('engineer.certificates.show', $cert->id) }}" class="text-gray-400 hover:text-marine-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 5 8.268 7.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p>No certificates found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($certificates->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $certificates->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
