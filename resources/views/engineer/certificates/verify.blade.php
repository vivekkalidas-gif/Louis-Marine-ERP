@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Verify Certificate</h2>
        <p class="text-gray-500 dark:text-gray-400">Review the certificate details below. As the assigned verifier, your approval is required to issue this certificate.</p>
    </div>

    <!-- Re-use Show View Content Structure (or include it if extracted) -->
    <!-- For simplicity, duplicating the read-only view structure here -->
    
    <!-- Details -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6 border-l-4 border-marine-500">
        <div class="px-4 py-5 sm:px-6 bg-marine-50 border-b border-marine-100 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-medium text-marine-900">Certificate Information</h3>
            <span class="inline-flex px-2 py-1 rounded text-xs font-semibold bg-marine-100 text-marine-800 uppercase">
                Creator: {{ $certificate->creator->name }}
            </span>
        </div>
        <div class="px-4 py-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Template</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ $certificate->certificateTemplate->name }}</dd>
            </div>
            <div>
                 <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer / Vessel</dt>
                 <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $certificate->customer->company_name }} / {{ $certificate->vessel->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Issue Date</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $certificate->issue_date ? $certificate->issue_date->format('d M, Y') : '-' }}</dd>
            </div>
        </div>
    </div>

    <!-- Dynamic Fields (Read Only) -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-8">
        <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-700 border-b border-gray-200">
             <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Inspection Data</h3>
        </div>
        <div class="p-6">
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
        </div>
    </div>

    <!-- Action Section -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden p-6 border border-gray-200">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Verification Actions</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Approve -->
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <h4 class="font-semibold text-green-900 mb-2">Approve Certificate</h4>
                <p class="text-sm text-green-700 mb-4">
                    By approving, you confirm that the data is correct. Your digital signature will be applied.
                </p>
                @if(auth()->user()->signature_path)
                    <div class="mb-4">
                        <p class="text-xs text-green-600 mb-1">Your Signature:</p>
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->signature_path) }}" alt="Signature" class="h-12 border border-green-200 bg-white dark:bg-gray-800 p-1">
                    </div>
                    <form action="{{ route('engineer.certificates.approve', $certificate->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="w-full py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium shadow-sm">
                            Approve & Issue
                        </button>
                    </form>
                @else
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                        <p class="font-bold">Signature Missing</p>
                        <p>You must upload your digital signature in your profile before you can approve certificates.</p>
                        <a href="{{ route('profile.edit') }}" class="underline mt-2 inline-block">Go to Profile</a>
                    </div>
                @endif
            </div>

            <!-- Reject -->
            <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                <h4 class="font-semibold text-red-900 mb-2">Reject Certificate</h4>
                <p class="text-sm text-red-700 mb-4">
                    Rejecting will solve the certificate back to DRAFT status for the creator to fix.
                </p>
                <form action="{{ route('engineer.certificates.approve', $certificate->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="reject">
                    <label class="block text-sm font-medium text-red-700 mb-1">Rejection Reason / Notes</label>
                    <textarea name="notes" rows="3" required class="w-full rounded-md border-red-300 focus:border-red-500 focus:ring-red-500 mb-4 text-sm" placeholder="Explain why..."></textarea>
                    
                    <button type="submit" class="w-full py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium shadow-sm">
                        Reject
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
