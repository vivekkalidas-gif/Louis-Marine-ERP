<?php
echo "<h2>Emergency: Restoring routes/web.php</h2>";

$file = __DIR__ . '/../routes/web.php';

// Read current content
$content = file_get_contents($file);

// Remove any malformed petty cash routes
$content = preg_replace('/\/\/\s*Petty Cash Module.*?(\n\s*Route::resource\(\'accounts\/petty-cash\'.*?\);?\s*\n)/s', '', $content);

// Now let's find a safe insertion point and add correctly
// Look for the closing of admin middleware group
if (preg_match('/(Route::resource\(\'staff\',.*?StaffController::class\);)/s', $content, $matches)) {
    $pettyCashRoute = "\n\n    // Petty Cash\n    Route::get('accounts/petty-cash', [\\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class, 'index'])->name('admin.accounts.petty-cash.index');\n    Route::post('accounts/petty-cash', [\\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class, 'store'])->name('admin.accounts.petty-cash.store');\n    Route::delete('accounts/petty-cash/{petty_cash}', [\\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class, 'destroy'])->name('admin.accounts.petty-cash.destroy');";
    
    // Check if not already added
    if (strpos($content, 'admin.accounts.petty-cash.index') === false) {
        $content = str_replace($matches[1], $matches[1] . $pettyCashRoute, $content);
    }
}

file_put_contents($file, $content);

echo "<p style='color:green'>✓ Routes file restored</p>";
echo "<p>Run: <a href='/clear_routes_cache.php'>clear_routes_cache.php</a></p>";
