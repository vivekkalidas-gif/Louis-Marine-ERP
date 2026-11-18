<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Authenticate or strictly limit this script? 
// For simplicity in this context, we'll assume the user deletes it immediately.
// Or we can check a simple query param ?key=123

echo "<pre>";
echo "<h2>Deployment Script</h2>";

try {
    echo "1. Clearing Caches...\n";
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo \Illuminate\Support\Facades\Artisan::output();
    
    echo "2. Running Migrations...\n";
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo \Illuminate\Support\Facades\Artisan::output();
    
    echo "3. Caching Routes & Config...\n";
    \Illuminate\Support\Facades\Artisan::call('optimize');
    echo \Illuminate\Support\Facades\Artisan::output();
    
    echo "\nDeployment Complete.\n";
    echo "IMPORTANT: Please delete this file (public/deploy_v2.php) immediately after use.";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
    echo "\n" . $e->getTraceAsString();
}

echo "</pre>";
