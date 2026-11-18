<?php
$file = __DIR__ . '/../routes/web.php';
$content = file_get_contents($file);

$routesToAdd = [
    'update' => "    Route::put('staff/ledger/{id}', [\\App\\Http\\Controllers\\Admin\\StaffController::class, 'updateLedgerEntry'])->name('staff.ledger.update');",
    'delete' => "    Route::delete('staff/ledger/{id}', [\\App\\Http\\Controllers\\Admin\\StaffController::class, 'destroyLedgerEntry'])->name('staff.ledger.destroy');",
    'print' => "    Route::get('staff/ledger/{id}/print', [\\App\\Http\\Controllers\\Admin\\StaffController::class, 'printLedgerEntry'])->name('staff.ledger.print');",
];

$added = [];
$existing = [];

foreach ($routesToAdd as $name => $route) {
    if (strpos($content, "staff.ledger.$name") === false) {
        // Find insertion point (after staff resource route)
        if (preg_match("/(Route::resource\('staff',.*?;)/", $content, $matches)) {
            $content = str_replace($matches[1], $matches[1] . "\n" . $route, $content);
            $added[] = $name;
        }
    } else {
        $existing[] = $name;
    }
}

file_put_contents($file, $content);

echo "<h3>Staff Ledger Routes Status:</h3>";
if (count($added) > 0) {
    echo "<p style='color:green'>✓ Added routes: " . implode(', ', $added) . "</p>";
}
if (count($existing) > 0) {
    echo "<p style='color:blue'>ℹ Already exists: " . implode(', ', $existing) . "</p>";
}
echo "<p><strong>Total routes configured: " . (count($added) + count($existing)) . "/3</strong></p>";
