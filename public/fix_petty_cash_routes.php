<?php
$file = __DIR__ . '/../routes/web.php';
$content = file_get_contents($file);

echo "<h3>Checking Petty Cash Routes</h3>";

// Remove old incorrect routes if they exist
$content = preg_replace('/\/\/\s*Petty Cash Module.*?\n.*?petty-cash.*?\n/s', '', $content);

// Define correct route
$routeCode = <<<'EOD'

    // Petty Cash Module
    Route::resource('accounts/petty-cash', \App\Http\Controllers\Admin\Accounts\PettyCashController::class)->only(['index', 'store', 'destroy'])->names([
        'index' => 'admin.accounts.petty-cash.index',
        'store' => 'admin.accounts.petty-cash.store',
        'destroy' => 'admin.accounts.petty-cash.destroy',
    ]);

EOD;

// Check if route exists
if (strpos($content, 'admin.accounts.petty-cash.index') !== false) {
    echo "<p style='color:blue'>ℹ Routes already exist with correct names</p>";
} else {
    // Find admin middleware group and insert
    if (preg_match("/(Route::middleware.*?'admin'.*?\{)/s", $content, $matches)) {
        $content = str_replace($matches[1], $matches[1] . $routeCode, $content);
        file_put_contents($file, $content);
        echo "<p style='color:green'>✓ Routes added with correct naming</p>";
    } else {
        echo "<p style='color:red'>✗ Could not find admin middleware group</p>";
    }
}

echo "<hr>";
echo "<p><strong>Now clear route cache:</strong></p>";
echo "<p>Run: <code>php artisan route:clear</code> or <code>php artisan optimize:clear</code></p>";
echo "<p>Or use: <a href='/clear_routes_cache.php'>clear_routes_cache.php</a></p>";
