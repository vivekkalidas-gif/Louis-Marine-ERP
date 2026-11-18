<?php
// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "<h2>Petty Cash Module - Complete Setup</h2>";
echo "<hr>";

// 1. Create Migration File
echo "<h3>Step 1: Creating Migration File</h3>";
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
    echo "<p style='color:green'>✓ Migration file created at: $migrationFile</p>";
} else {
    echo "<p style='color:blue'>ℹ Migration file already exists</p>";
}

// 2. Run Migration
echo "<h3>Step 2: Running Migration</h3>";
try {
    Artisan::call('migrate', ['--force' => true]);
    $output = Artisan::output();
    echo "<pre style='background:#f0f0f0;padding:10px'>$output</pre>";
    echo "<p style='color:green'>✓ Migration executed successfully</p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>✗ Migration error: " . $e->getMessage() . "</p>";
}

// 3. Create Model
echo "<h3>Step 3: Creating Model</h3>";
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
    echo "<p style='color:green'>✓ Model created at: $modelFile</p>";
} else {
    echo "<p style='color:blue'>ℹ Model already exists</p>";
}

// 4. Create Controller
echo "<h3>Step 4: Creating Controller</h3>";
$controllerDir = __DIR__ . '/../app/Http/Controllers/Admin/Accounts';
$controllerFile = $controllerDir . '/PettyCashController.php';

if (!is_dir($controllerDir)) {
    mkdir($controllerDir, 0755, true);
    echo "<p style='color:green'>✓ Created directory: $controllerDir</p>";
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
    echo "<p style='color:green'>✓ Controller created at: $controllerFile</p>";
} else {
    echo "<p style='color:blue'>ℹ Controller already exists</p>";
}

// 5. Add Routes
echo "<h3>Step 5: Adding Routes</h3>";
$routesFile = __DIR__ . '/../routes/web.php';
$content = file_get_contents($routesFile);

if (strpos($content, 'petty-cash') === false) {
    $routeCode = "\n    // Petty Cash Module (Added " . date('Y-m-d H:i:s') . ")\n";
    $routeCode .= "    Route::prefix('accounts')->as('accounts.')->group(function() {\n";
    $routeCode .= "        Route::resource('petty-cash', \\App\\Http\\Controllers\\Admin\\Accounts\\PettyCashController::class)->only(['index', 'store', 'destroy']);\n";
    $routeCode .= "    });\n";
    
    // Find admin middleware group
    if (preg_match("/(Route::middleware.*?'admin'.*?\{)/s", $content, $matches)) {
        $content = str_replace($matches[1], $matches[1] . $routeCode, $content);
        file_put_contents($routesFile, $content);
        echo "<p style='color:green'>✓ Routes added to web.php</p>";
    } else {
        echo "<p style='color:red'>✗ Could not find admin middleware group in routes</p>";
    }
} else {
    echo "<p style='color:blue'>ℹ Petty Cash routes already exist</p>";
}

echo "<hr>";
echo "<h2 style='color:green'>✅ SETUP COMPLETE!</h2>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Run: <a href='/create_petty_cash_view.php'>create_petty_cash_view.php</a> to create the view</li>";
echo "<li>Run: <a href='/clear_views_now.php'>clear_views_now.php</a> to clear cache</li>";
echo "<li>Navigate to Petty Cash in sidebar</li>";
echo "</ol>";
