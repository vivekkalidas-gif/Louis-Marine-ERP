<?php
/**
 * Production Fix Script - Version 6.0
 * Upload this to your 'public' folder and visit in browser.
 */

$baseDir = __DIR__ . '/..';
require $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<html><body style='font-family: monospace; background: #1a202c; color: #a0aec0; padding: 20px;'>";
echo "<h1>System Maintenance Output v6</h1>";
echo "<div style='background: #2d3748; padding: 15px; border-radius: 8px; overflow: auto;'>";

try {
    echo "<h3 style='color: #63b3ed;'>1. Fixing Schema Drifts...</h3>";

    // 1. Chart of Accounts (Add Missing Columns manually)
    if (Illuminate\Support\Facades\Schema::hasTable('chart_of_accounts')) {
        echo "Checking 'chart_of_accounts'...<br>";
        
        // is_reconcilable
        if (!Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'is_reconcilable')) {
            Illuminate\Support\Facades\Schema::table('chart_of_accounts', function ($table) {
                $table->boolean('is_reconcilable')->default(false)->after('type');
            });
            echo "- Add: is_reconcilable <span style='color:green'>Done</span><br>";
        }

        // parent_id (Fixes MissingAttributeException)
        if (!Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'parent_id')) {
            Illuminate\Support\Facades\Schema::table('chart_of_accounts', function ($table) {
                $table->uuid('parent_id')->nullable()->after('id');
            });
            echo "- Add: parent_id <span style='color:green'>Done</span><br>";
        }

        // description
        if (!Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'description')) {
             Illuminate\Support\Facades\Schema::table('chart_of_accounts', function ($table) {
                $table->text('description')->nullable()->after('name');
            });
            echo "- Add: description <span style='color:green'>Done</span><br>";
        }

        // is_active
        if (!Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'is_active')) {
             Illuminate\Support\Facades\Schema::table('chart_of_accounts', function ($table) {
                $table->boolean('is_active')->default(true);
            });
            echo "- Add: is_active <span style='color:green'>Done</span><br>";
        }
    }

    // 2. Journal Entries
    if (Illuminate\Support\Facades\Schema::hasTable('journal_entries')) {
        echo "Checking 'journal_entries'...<br>";
        if (!Illuminate\Support\Facades\Schema::hasColumn('journal_entries', 'partner_id')) {
             Illuminate\Support\Facades\Schema::table('journal_entries', function ($table) {
                $table->uuid('partner_id')->nullable()->after('state');
                $table->uuid('created_by')->nullable()->after('partner_id');
            });
            echo "- Add: partner_id, created_by <span style='color:green'>Done</span><br>";
        }
    }

    // 3. Journal Items Check
    if (Illuminate\Support\Facades\Schema::hasTable('journal_items')) {
        echo "Checking 'journal_items'...<br>";
        // Label
        if (!Illuminate\Support\Facades\Schema::hasColumn('journal_items', 'label')) {
             Illuminate\Support\Facades\Schema::table('journal_items', function ($table) {
                $table->string('label')->nullable()->after('partner_id');
            });
            echo "- Add: label <span style='color:green'>Done</span><br>";
        }
        
        // Drop bad foreign key
        try {
            Illuminate\Support\Facades\Schema::table('journal_items', function ($table) {
                $table->dropForeign('journal_items_partner_id_foreign');
            });
            echo "- Drop FK: journal_items_partner_id_foreign <span style='color:green'>Done</span><br>";
        } catch (\Exception $e) { 
            // Ignore if already dropped
        }
    }

    // 4. Clear Caches
    echo "<h3 style='color: #63b3ed;'>2. Clearing Caches...</h3>";
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    echo "Done.<br>";

    echo "<h2 style='color: #48bb78;'>SUCCESS: Schema Repaired.</h2>";
    echo "<p>Please verify the Chart of Accounts page now.</p>";

} catch (\Exception $e) {
    echo "<h2 style='color: #f56565;'>ERROR:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
echo "</div></body></html>";
