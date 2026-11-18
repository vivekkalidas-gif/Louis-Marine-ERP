<?php
echo "<h2>Petty Cash Module - Complete Setup</h2>";

// 1. Create Migration
$migrationFile = __DIR__ . '/../database/migrations/2026_01_28_000002_create_petty_cash_entries_table.php';
if (!file_exists($migrationFile)) {
    $migrationContent = <<<'EOD'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->enum('type', ['deposit', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->string('received_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_entries');
    }
};
EOD;
    file_put_contents($migrationFile, $migrationContent);
    echo "<p>✓ Migration file created</p>";
} else {
    echo "<p>ℹ Migration already exists</p>";
}

// 2. Run Migration
try {
    Artisan::call('migrate', ['--force' => true]);
    echo "<p style='color:green'>✓ Migration executed: " . Artisan::output() . "</p>";
} catch (\Exception $e) {
    echo "<p style='color:orange'>⚠ Migration: " . $e->getMessage() . "</p>";
}

// 3. Create Model
$modelFile = __DIR__ . '/../app/Models/PettyCashEntry.php';
if (!file_exists($modelFile)) {
    $modelContent = <<<'EOD'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PettyCashEntry extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'date',
        'type',
        'amount',
        'description',
        'received_by',
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];
}
EOD;
    file_put_contents($modelFile, $modelContent);
    echo "<p>✓ Model created</p>";
} else {
    echo "<p>ℹ Model already exists</p>";
}

// 4. Create Controller
$controllerDir = __DIR__ . '/../app/Http/Controllers/Admin/Accounts';
$controllerFile = $controllerDir . '/PettyCashController.php';

if (!is_dir($controllerDir)) {
    mkdir($controllerDir, 0755, true);
}

if (!file_exists($controllerFile)) {
    $controllerContent = <<<'EOD'
<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\PettyCashEntry;
use Illuminate\Http\Request;

class PettyCashController extends Controller
{
    public function index()
    {
        $entries = PettyCashEntry::orderBy('date', 'desc')->get();
        
        $totalDeposits = PettyCashEntry::where('type', 'deposit')->sum('amount');
        $totalExpenses = PettyCashEntry::where('type', 'expense')->sum('amount');
        $balance = $totalDeposits - $totalExpenses;
        
        return view('admin.accounts.petty_cash.index', compact('entries', 'balance', 'totalDeposits', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:deposit,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'received_by' => 'nullable|string',
            'approved_by' => 'nullable|string',
        ]);

        PettyCashEntry::create($validated);

        return back()->with('success', 'Entry recorded successfully.');
    }

    public function destroy($id)
    {
        PettyCashEntry::findOrFail($id)->delete();
        return back()->with('success', 'Entry deleted successfully.');
    }
}
EOD;
    file_put_contents($controllerFile, $controllerContent);
    echo "<p>✓ Controller created</p>";
} else {
    echo "<p>ℹ Controller already exists</p>";
}

// 5. Add Routes
$routesFile = __DIR__ . '/../routes/web.php';
$content = file_get_contents($routesFile);

if (strpos($content, 'petty-cash') === false) {
    $routeCode = "\n    // Petty Cash Module\n    Route::resource('accounts/petty-cash', \\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class)->only(['index', 'store', 'destroy']);\n";
    
    if (preg_match("/(Route::middleware.*?'admin'.*?\{)/", $content, $matches)) {
        $content = str_replace($matches[1], $matches[1] . $routeCode, $content);
        file_put_contents($routesFile, $content);
        echo "<p>✓ Routes added</p>";
    }
} else {
    echo "<p>ℹ Routes already exist</p>";
}

echo "<h3 style='color:green'>✓ Petty Cash Module Installation Complete!</h3>";
echo "<p>Now upload the view file: <code>resources/views/admin/accounts/petty_cash/index.blade.php</code></p>";
