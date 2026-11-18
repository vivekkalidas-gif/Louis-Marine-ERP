<?php
$file = __DIR__ . '/../resources/views/layouts/sidebars/default.blade.php';
$content = file_get_contents($file);

// Define the Petty Cash link
$pettyCashLink = <<<'EOD'

        @if(auth()->user()->hasRole(['admin', 'accountant']))
        <a href="{{ route('admin.accounts.petty-cash.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.accounts.petty-cash.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>{{ __("Petty Cash") }}
        </a>
        @endif
EOD;

// Check if already added
if (strpos($content, 'Petty Cash') !== false) {
    echo "<p style='color:blue'>ℹ Petty Cash link already exists in sidebar</p>";
} else {
    // Find the Payroll Generator line and add after it
    if (preg_match('/(.*Payroll Generator.*<\/a>\s*@endif)/s', $content, $matches)) {
        $content = str_replace($matches[1], $matches[1] . $pettyCashLink, $content);
        file_put_contents($file, $content);
        echo "<p style='color:green'>✓ Petty Cash link added to sidebar after Payroll Generator</p>";
    } else {
        echo "<p style='color:red'>✗ Could not find insertion point</p>";
    }
}

echo "<p><strong>Clear cache:</strong> <a href='/clear_views_now.php'>clear_views_now.php</a></p>";
