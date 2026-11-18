<?php
echo "<h2>Copying Working Routes File from Local to Production</h2>";

$localFile = __DIR__ . '/../routes/web.php';
$backupFile = __DIR__ . '/../routes/web.php.backup.' . date('YmdHis');

// Create backup
if (file_exists($localFile)) {
    copy($localFile, $backupFile);
    echo "<p style='color:blue'>✓ Backup created: " . basename($backupFile) . "</p>";
}

// Read the working local routes
$localContent = file_get_contents($localFile);

// Find where to insert Petty Cash routes (after staff routes in admin group)
$searchPattern = "Route::resource('staff', \\App\\Http\\Controllers\\Admin\\StaffController::class);";

if (strpos($localContent, $searchPattern) !== false && strpos($localContent, 'petty-cash') === false) {
    $pettyCashRoutes = "\n\n    // Petty Cash Module\n";
    $pettyCashRoutes .= "    Route::get('accounts/petty-cash', [\\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class, 'index'])->name('admin.accounts.petty-cash.index');\n";
    $pettyCashRoutes .= "    Route::post('accounts/petty-cash', [\\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class, 'store'])->name('admin.accounts.petty-cash.store');\n";
    $pettyCashRoutes .= "    Route::delete('accounts/petty-cash/{petty_cash}', [\\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class, 'destroy'])->name('admin.accounts.petty-cash.destroy');\n";
    
    $localContent = str_replace($searchPattern, $searchPattern . $pettyCashRoutes, $localContent);
    
    file_put_contents($localFile, $localContent);
    echo "<p style='color:green'>✓ Petty Cash routes added to web.php</p>";
} elseif (strpos($localContent, 'petty-cash') !== false) {
    echo "<p style='color:blue'>ℹ Petty Cash routes already exist</p>";
} else {
    echo "<p style='color:red'>✗ Could not find staff route for insertion</p>";
}

echo "<hr>";
echo "<p><strong>Next: Clear cache</strong> - <a href='/clear_routes_cache.php'>clear_routes_cache.php</a></p>";
