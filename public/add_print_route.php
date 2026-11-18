<?php
$file = __DIR__ . '/../routes/web.php';
$content = file_get_contents($file);

// Check if print route exists
if (strpos($content, 'staff/ledger/{id}/print') === false) {
    // Find the admin middleware group closing or a good insertion point
    // Add after other staff routes
    $insertCode = "\n    // Staff Ledger Print Route\n    Route::get('staff/ledger/{id}/print', [\\App\\Http\\Controllers\\Admin\\StaffController::class, 'printLedgerEntry'])->name('staff.ledger.print');\n";
    
    // Find where to insert (after staff resource route)
    if (preg_match("/(Route::resource\('staff',.*?;)/", $content, $matches)) {
        $content = str_replace($matches[1], $matches[1] . $insertCode, $content);
        file_put_contents($file, $content);
        echo "✓ Print route added after staff resource route";
    } else {
        echo "✗ Could not find insertion point";
    }
} else {
    echo "✓ Print route already exists";
}
