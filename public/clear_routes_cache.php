<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<h3>Clearing Route Cache</h3>";

try {
    Artisan::call('route:clear');
    echo "<p style='color:green'>✓ Route cache cleared</p>";
    echo "<pre>" . Artisan::output() . "</pre>";
} catch (\Exception $e) {
    echo "<p style='color:red'>✗ Error: " . $e->getMessage() . "</p>";
}

try {
    Artisan::call('config:clear');
    echo "<p style='color:green'>✓ Config cache cleared</p>";
} catch (\Exception $e) {
    echo "<p style='color:orange'>⚠ Config: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Cache cleared! Refresh your page.</strong></p>";
