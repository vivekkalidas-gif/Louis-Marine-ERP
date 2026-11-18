<?php
$file = __DIR__ . '/../routes/web.php';
$content = file_get_contents($file);

echo "<h3>Grepping 'printLedgerEntry' in web.php</h3>";
$pos = strpos($content, 'printLedgerEntry');

if ($pos !== false) {
    echo "Found at position: $pos<br>";
    $context = substr($content, max(0, $pos - 100), 200);
    echo "<pre>" . htmlspecialchars($context) . "</pre>";
} else {
    echo "NOT FOUND.<br>";
    
    // Explicitly Append it now
    $code = "\n" . <<<'PHP'
Route::middleware(['web', 'auth'])->prefix('admin')->group(function() {
    Route::put('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'updateLedgerEntry'])->name('staff.ledger.update');
    Route::delete('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroyLedgerEntry'])->name('staff.ledger.destroy');
    Route::get('staff/ledger/{id}/print', [\App\Http\Controllers\Admin\StaffController::class, 'printLedgerEntry'])->name('staff.ledger.print');
});
PHP;
    file_put_contents($file, $code, FILE_APPEND);
    echo "Appended new routes to end of file.<br>";
}

// Clear Cache again
$cacheFile = __DIR__ . '/../bootstrap/cache/routes-v7.php';
if (file_exists($cacheFile)) {
    unlink($cacheFile);
    echo "Deleted route cache.";
}
