<?php
// rescue.php - Place this in your 'public' folder (next to index.php) and visit /rescue.php

echo "<h1>Louis ERP - Deployment Rescue Script</h1>";
echo "<pre>";

function check($label, $condition) {
    echo $label . ": " . ($condition ? "<span style='color:green'>OK</span>" : "<span style='color:red'>FAILED</span>") . "\n";
}

// 1. Define Paths
$baseDir = dirname(__DIR__);
$bootstrapCache = $baseDir . '/bootstrap/cache';
$vendorDir = $baseDir . '/vendor';
$carbonLang = $vendorDir . '/nesbot/carbon/src/Carbon/Lang/en.php';

echo "Base Dir: $baseDir\n\n";

// 2. Clear Cache Files manually
echo "--- Step 1: Clearing Bootstrap Cache ---\n";
$files = glob($bootstrapCache . '/*.php');
foreach ($files as $file) {
    if (is_file($file)) {
        if (unlink($file)) {
            echo "Deleted: " . basename($file) . "\n";
        } else {
            echo "ERROR: Could not delete " . basename($file) . " (Check Permissions)\n";
        }
    }
}
echo "Cache cleared (if any existed).\n\n";

// 3. Check Vendor & Carbon
echo "--- Step 2: Checking Vendor Files ---\n";
check("Vendor Directory Exists", is_dir($vendorDir));
check("Carbon Directory Exists", is_dir($vendorDir . '/nesbot/carbon'));
check("Carbon Language File (en.php) Exists", file_exists($carbonLang));

if (!file_exists($carbonLang)) {
    echo "\nCRITICAL ERROR: Carbon Language files are missing!\n";
    echo "Your upload of 'vendor.zip' might be incomplete or the extraction failed.\n";
    echo "Expected path: $carbonLang\n";
}

// 4. Check Permissions
echo "\n--- Step 3: Checking Permissions ---\n";
check("Storage Writable", is_writable($baseDir . '/storage'));
check("Bootstrap Cache Writable", is_writable($bootstrapCache));

// 5. Try to Run Artisan
echo "\n--- Step 4: Attempting Artisan Optimize ---\n";
try {
    define('LARAVEL_START', microtime(true));
    require $baseDir . '/vendor/autoload.php';
    $app = require $baseDir . '/bootstrap/app.php';
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $status = $kernel->call('optimize:clear');
    echo "Artisan Output: " . $kernel->output();
} catch (\Throwable $e) {
    echo "Artisan Failed: " . $e->getMessage() . "\n";
    echo "Stack Trace: " . $e->getTraceAsString();
}

echo "</pre>";
