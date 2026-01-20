@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Create Landed Cost</h1>
        <a href="{{ route('accounts.landed-costs.index') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:text-gray-100">Cancel</a>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('accounts.landed-costs.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Purchase Order</label>
                <select name="purchase_order_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Select PO --</option>
                    @foreach($purchaseOrders as $po)
                        <option value="{{ $po->id }}">{{ $po->po_number }} - {{ $po->supplier ? $po->supplier->name : 'N/A' }} ({{ $po->po_date }})</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only showing recent Purchase Orders.</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
                    <input type="text" name="reference" placeholder="e.g. Shipment #123" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Create Draft</button>
            </div>
        </form>
    </div>
</div>
@endsection
