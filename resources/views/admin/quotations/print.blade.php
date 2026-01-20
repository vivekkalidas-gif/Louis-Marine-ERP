<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-800 p-8 max-w-4xl mx-auto">
    
    <!-- Print Controls -->
    <div class="mb-8 text-right no-print">
        <button onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">Print / Save as PDF</button>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-start border-b pb-8 mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-200 mb-2">QUOTATION</h1>
            <p class="text-gray-600"># {{ $quotation->quotation_number }}</p>
            <p class="text-gray-600">Date: {{ $quotation->date->format('d M Y') }}</p>
            <p class="text-gray-600">Valid Until: {{ $quotation->valid_until->format('d M Y') }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-bold text-blue-900 mb-2">Louis Marine Qatar</h2>
            <p class="text-gray-600 text-sm">Unit 42, Marine Industrial Park</p>
            <p class="text-gray-600 text-sm">Doha, Qatar</p>
            <p class="text-gray-600 text-sm">info@louismarine.qa</p>
        </div>
    </div>

    <!-- Prepared For -->
    <div class="mb-8">
        <h3 class="text-gray-500 dark:text-gray-400 font-bold uppercase text-xs mb-2">Prepared For</h3>
        <p class="font-bold text-xl">{{ $quotation->customer->company_name }}</p>
        <p class="text-gray-600">{{ $quotation->customer->email }}</p>
        <p class="text-gray-600">{{ $quotation->customer->phone }}</p>
        @if($quotation->job)
        <div class="mt-4">
            <h3 class="text-gray-500 dark:text-gray-400 font-bold uppercase text-xs mb-1">Reference</h3>
            <p class="text-sm">Job: {{ $quotation->job->job_number }} - {{ $quotation->job->vessel->name ?? '' }}</p>
        </div>
        @endif
    </div>

    <!-- Items -->
    <table class="w-full mb-8">
        <thead>
            <tr class="border-b-2 border-gray-800">
                <th class="text-left py-3 font-bold uppercase text-xs text-gray-600">Description</th>
                <th class="text-right py-3 font-bold uppercase text-xs text-gray-600 w-24">Qty</th>
                <th class="text-right py-3 font-bold uppercase text-xs text-gray-600 w-32">Unit Price</th>
                <th class="text-right py-3 font-bold uppercase text-xs text-gray-600 w-32">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $item)
            <tr class="border-b border-gray-200">
                <td class="py-4 text-sm font-bold text-gray-800 dark:text-gray-200">{{ $item->description }}</td>
                <td class="py-4 text-sm text-right text-gray-600">{{ $item->quantity }}</td>
                <td class="py-4 text-sm text-right text-gray-600">{{ number_format($item->unit_price, 2) }}</td>
                <td class="py-4 text-sm text-right text-gray-800 dark:text-gray-200 font-bold">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end mb-12">
        <div class="w-1/2">
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-bold">{{ number_format($quotation->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-gray-100">
                <span class="text-gray-600">VAT (5%)</span>
                <span class="font-bold">{{ number_format($quotation->tax, 2) }}</span>
            </div>
            <div class="flex justify-between py-2 text-xl mt-2">
                <span class="font-bold">Total (QAR)</span>
                <span class="font-bold text-blue-900">{{ number_format($quotation->total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Terms -->
     <div class="border-t pt-8">
        <h3 class="font-bold text-sm mb-2">Terms & Conditions</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-wrap">{{ $quotation->terms }}</p>
    </div>

</body>
</html>
