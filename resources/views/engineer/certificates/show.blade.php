@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Certificate Details</h2>
            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                <span>{{ $certificate->certificate_number ?? 'DRAFT' }}</span>
                <span class="text-gray-300">|</span>
                <span>{{ $certificate->certificateTemplate->name }}</span>
            </div>
        </div>
        <div class="flex gap-3">
             @if($certificate->status === 'ISSUED')
                <form action="{{ route('engineer.certificates.download', $certificate->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-marine-600 text-marine-600 rounded-md text-sm font-medium hover:bg-marine-50">
                        Download PDF
                    </button>
                </form>
            @endif
            <a href="{{ route('engineer.certificates.index') }}" class="px-4 py-2 text-gray-600 bg-white dark:bg-gray-800 border border-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 text-sm font-medium">Back</a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mb-6 rounded-lg p-4 {{ $certificate->status === 'ISSUED' ? 'bg-green-50 border border-green-200' : ($certificate->status === 'REJECTED' ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200') }}">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <!-- Icon based on status -->
                @if($certificate->status === 'ISSUED')
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                @elseif($certificate->status === 'REJECTED')
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                @else
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                @endif
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium {{ $certificate->status === 'ISSUED' ? 'text-green-800' : ($certificate->status === 'REJECTED' ? 'text-red-800' : 'text-yellow-800') }}">
                    Status: {{ str_replace('_', ' ', $certificate->status) }}
                </p>
                <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    <p>Created by: <strong>{{ $certificate->creator->name }}</strong> on {{ $certificate->created_at->format('d M, Y') }}</p>
                    <p class="mt-1">Assigned Verifier: <strong>{{ $certificate->verifier->name ?? 'None' }}</strong></p>
                    @if($certificate->verified_at)
                    <p class="mt-1">Verified on: {{ $certificate->verified_at->format('d M, Y H:i') }}</p>
                    @endif
                    @if($certificate->verification_notes)
                    <p class="mt-2 p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 text-red-600 text-xs">
                        <strong>Rejection Reason:</strong> {{ $certificate->verification_notes }}
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Details -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Certificate Information</h3>
        </div>
        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $certificate->customer->company_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Vessel</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $certificate->vessel->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Issue Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $certificate->issue_date ? $certificate->issue_date->format('d M, Y') : '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Expiry Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d M, Y') : '-' }}</dd>
            </div>
        </div>
    </div>

    <!-- Dynamic Fields (Read Only) -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200">
             <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Inspection Data</h3>
        </div>
        <div class="p-6">
            <!-- Group values by Section -->
            @php
                $groupedValues = $certificate->fieldValues->groupBy(function($val) {
                    return $val->definition->section->name ?? 'Other';
                });
            @endphp

            @foreach($groupedValues as $sectionName => $values)
                <div class="mb-6 last:mb-0">
                    <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b border-gray-100 pb-1">{{ $sectionName }}</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                        @foreach($values as $val)
                            <div class="sm:col-span-1">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $val->definition->field_label }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded">
                                    {{ $val->value ?? '-' }}
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endforeach
            
            @if($groupedValues->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 italic">No dynamic data recorded.</p>
            @endif
        </div>
    </div>
</div>
@endsection
