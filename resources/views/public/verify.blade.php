<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Certificate - Louis Marine Qatar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white dark:bg-gray-800 shadow-xl rounded-lg overflow-hidden">
        <div class="bg-blue-900 p-6 text-center">
            <h1 class="text-2xl font-bold text-white uppercase tracking-widest">Certificate Verification</h1>
        </div>
        
        <div class="p-8 text-center">
            @if($isExpired)
                <div class="bg-red-100 text-red-800 p-4 rounded mb-6">
                    <svg title="Expired" class="w-16 h-16 mx-auto mb-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h2 class="text-xl font-bold">EXPIRED</h2>
                    <p>This certificate is no longer valid.</p>
                </div>
            @else
                <div class="bg-green-100 text-green-800 p-4 rounded mb-6">
                    <svg title="Valid" class="w-16 h-16 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h2 class="text-xl font-bold">VALID</h2>
                    <p>This certificate is active and authentic.</p>
                </div>
            @endif

            <div class="text-left space-y-3 text-sm text-gray-700 dark:text-gray-300">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Certificate No:</span>
                    <span class="font-mono font-bold">{{ $certificate->certificate_number }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Type:</span>
                    <span class="font-bold">{{ $certificate->type->name ?? 'Certificate' }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Vessel:</span>
                    <span class="font-bold">{{ $certificate->vessel->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-500 dark:text-gray-400">Expiry Date:</span>
                    <span class="font-bold {{ $isExpired ? 'text-red-600' : '' }}">{{ $certificate->expiry_date ? $certificate->expiry_date->format('d M Y') : 'N/A' }}</span>
                </div>
            </div>

            <div class="mt-8 text-xs text-gray-400">
                <p>Louis Marine Qatar &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
