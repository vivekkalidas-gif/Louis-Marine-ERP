<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<h2>Adding Missing Columns to petty_cash_entries Table</h2>";

try {
    // Check if columns exist
    $hasReceivedBy = \Illuminate\Support\Facades\Schema::hasColumn('petty_cash_entries', 'received_by');
    $hasApprovedBy = \Illuminate\Support\Facades\Schema::hasColumn('petty_cash_entries', 'approved_by');
    
    echo "<p>received_by column exists: " . ($hasReceivedBy ? 'YES' : 'NO') . "</p>";
    echo "<p>approved_by column exists: " . ($hasApprovedBy ? 'YES' : 'NO') . "</p>";
    
    if (!$hasReceivedBy || !$hasApprovedBy) {
        \Illuminate\Support\Facades\Schema::table('petty_cash_entries', function ($table) use ($hasReceivedBy, $hasApprovedBy) {
            if (!$hasReceivedBy) {
                $table->string('received_by')->nullable()->after('description');
            }
            if (!$hasApprovedBy) {
                $table->string('approved_by')->nullable()->after('received_by');
            }
        });
        
        echo "<p style='color:green'><strong>✓ Missing columns added successfully!</strong></p>";
    } else {
        echo "<p style='color:blue'>ℹ All required columns already exist</p>";
    }
    
    // Show current table structure
    echo "<h3>Current Table Structure:</h3>";
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE petty_cash_entries');
    echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    foreach ($columns as $column) {
        echo "<tr><td>{$column->Field}</td><td>{$column->Type}</td><td>{$column->Null}</td><td>{$column->Default}</td></tr>";
    }
    echo "</table>";
    
} catch (\Exception $e) {
    echo "<p style='color:red'><strong>✗ Error:</strong> " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Now try adding an entry in Petty Cash!</strong></p>";
