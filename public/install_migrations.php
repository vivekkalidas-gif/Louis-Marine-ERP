<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Artisan;

// 1. Create Deductions Migration
$deductionsContent = <<<'EOD'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('payroll_deductions')) {
            Schema::create('payroll_deductions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                $table->string('month'); // Format 'YYYY-MM'
                $table->decimal('amount', 12, 2);
                $table->string('description')->nullable(); // e.g., 'Advance Repayment', 'Sick Leave'
                $table->foreignUuid('created_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
EOD;

file_put_contents(__DIR__ . '/../database/migrations/2026_01_28_000000_create_payroll_deductions_table.php', $deductionsContent);
echo "Created Deductions Migration.\n";

// 2. Create Ledger Entries Migration
$ledgerContent = <<<'EOD'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('employee_ledger_entries')) {
            Schema::create('employee_ledger_entries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->string('type'); // 'advance_given', 'repayment', 'salary_credit', 'other'
                $table->decimal('amount', 12, 2); 
                $table->decimal('debit', 12, 2)->default(0); // Money User Owes (Advance Given)
                $table->decimal('credit', 12, 2)->default(0); // Money User Paid Back (Deduction)
                $table->string('description')->nullable();
                $table->string('reference')->nullable(); // Link to payroll_id or transaction_id
                $table->foreignUuid('created_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employee_ledger_entries');
    }
};
EOD;

file_put_contents(__DIR__ . '/../database/migrations/2026_01_28_000001_create_employee_ledger_entries_table.php', $ledgerContent);
echo "Created Ledger Migration.\n";

// 3. Run Migrate
echo "Running Migrations...\n";
try {
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    echo "Migrations Completed Successfully.";
} catch (\Exception $e) {
    echo "Migration Message: " . $e->getMessage();
    echo "\nTrace: " . $e->getTraceAsString();
}
