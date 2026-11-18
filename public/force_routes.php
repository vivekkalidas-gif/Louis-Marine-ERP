<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
use Illuminate\Support\Facades\Artisan;

$file = __DIR__ . '/../routes/web.php';
$content = file_get_contents($file);

echo "Checking routes file...\n";

if (str_contains($content, "name('staff.ledger.print')")) {
    echo "Routes already appear to be present in web.php. Maybe cache is the issue?\n";
} else {
    echo "Routes NOT found. Appending them now...\n";
    
    // Append at the end of the file. 
    // We wrap in a generic middleware group to match Admin panel expectations regarding Auth
    $newRoutes = "\n" . <<<'EOD'

// STAFF LEDGER CUSTOM ROUTES (Appended by Force Script)
Route::middleware(['web', 'auth'])->prefix('admin')->group(function() {
    Route::put('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'updateLedgerEntry'])->name('staff.ledger.update');
    Route::delete('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroyLedgerEntry'])->name('staff.ledger.destroy');
    Route::get('staff/ledger/{id}/print', [\App\Http\Controllers\Admin\StaffController::class, 'printLedgerEntry'])->name('staff.ledger.print');
});
EOD;

    file_put_contents($file, $newRoutes, FILE_APPEND);
    echo "Routes appended successfully.\n";
}

echo "Clearing Route Cache...\n";
try {
    Artisan::call('route:clear');
    echo "Route Cache Cleared.\n";
} catch (\Exception $e) {
    echo "Error clearing cache: " . $e->getMessage();
}
