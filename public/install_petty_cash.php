<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
use Illuminate\Support\Facades\Artisan;

echo "Installing Petty Cash Module...\n";

// 1. Create Migration
$migrationContent = <<<'EOD'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('petty_cash_entries')) {
            Schema::create('petty_cash_entries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('date');
                $table->string('type'); // 'deposit' (In), 'expense' (Out)
                $table->decimal('amount', 12, 2);
                $table->string('category')->nullable(); // Food, Travel, Office
                $table->string('description');
                $table->string('receipt_path')->nullable();
                $table->foreignUuid('created_by')->nullable()->constrained('users');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('petty_cash_entries');
    }
};
EOD;
file_put_contents(__DIR__ . '/../database/migrations/2026_01_28_000002_create_petty_cash_entries_table.php', $migrationContent);
echo "1. Migration created.\n";

// 2. Run Migration
try {
    Artisan::call('migrate', ['--force' => true]);
    echo "2. Migration ran successfully.\n";
} catch (\Exception $e) {
    echo "2. Migration skipped/error: " . $e->getMessage() . "\n";
}

// 3. Create Model
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
        'category',
        'description',
        'receipt_path',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
EOD;
file_put_contents(__DIR__ . '/../app/Models/PettyCashEntry.php', $modelContent);
echo "3. Model created.\n";

// 4. Create Controller
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
        $entries = PettyCashEntry::orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(50);
        
        $totalDeposits = PettyCashEntry::where('type', 'deposit')->sum('amount');
        $totalExpenses = PettyCashEntry::where('type', 'expense')->sum('amount');
        $balance = $totalDeposits - $totalExpenses;

        return view('admin.accounts.petty_cash.index', compact('entries', 'balance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:deposit,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'category' => 'nullable|string',
        ]);

        PettyCashEntry::create([
            'date' => $request->date,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'category' => $request->category,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Entry recorded successfully.');
    }

    public function destroy($id)
    {
        $entry = PettyCashEntry::findOrFail($id);
        $entry->delete();
        return back()->with('success', 'Entry deleted successfully.');
    }
}
EOD;
@mkdir(__DIR__ . '/../app/Http/Controllers/Admin/Accounts', 0755, true);
file_put_contents(__DIR__ . '/../app/Http/Controllers/Admin/Accounts/PettyCashController.php', $controllerContent);
echo "4. Controller created.\n";

// 5. Create View
$viewContent = <<<'EOD'
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Petty Cash Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Balance Card -->
            <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400">Current Cash on Hand</h3>
                        <p class="text-4xl font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($balance, 2) }}
                        </p>
                    </div>
                    <button onclick="document.getElementById('entryModal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        + New Entry
                    </button>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Transaction History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cash In</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cash Out</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($entries as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $entry->date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $entry->description }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $entry->category ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-green-600">
                                        {{ $entry->type === 'deposit' ? number_format($entry->amount, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-red-600">
                                        {{ $entry->type === 'expense' ? number_format($entry->amount, 2) : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ route('accounts.petty-cash.destroy', $entry->id) }}" method="POST" onsubmit="return confirm('Delete this entry?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $entries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="entryModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('entryModal').classList.add('hidden')"></div>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('accounts.petty-cash.store') }}" method="POST">
                    @csrf
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-title">New Transaction</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                <select name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                                    <option value="expense">Expense (Cash Out)</option>
                                    <option value="deposit">Deposit (Cash In)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <input type="text" name="category" list="categories" placeholder="e.g. Food, Travel" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                <datalist id="categories">
                                    <option value="Food & Beverages">
                                    <option value="Travel & Transport">
                                    <option value="Office Supplies">
                                    <option value="Maintenance">
                                    <option value="Miscellaneous">
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <input type="text" name="description" placeholder="Details..." class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                                <input type="number" name="amount" min="0.01" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" required>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                        <button type="button" onclick="document.getElementById('entryModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
EOD;
@mkdir(__DIR__ . '/../resources/views/admin/accounts/petty_cash', 0755, true);
file_put_contents(__DIR__ . '/../resources/views/admin/accounts/petty_cash/index.blade.php', $viewContent);
echo "5. View created.\n";

// 6. Update Routes
$routesPath = __DIR__ . '/../routes/web.php';
$routesContent = file_get_contents($routesPath);
if (!str_contains($routesContent, 'PettyCashController')) {
    $route = "\n    Route::resource('accounts/petty-cash', \App\Http\Controllers\Admin\Accounts\PettyCashController::class)->names('accounts.petty-cash');";
    // Look for Accounts group
    if (str_contains($routesContent, "prefix('accounts')")) {
        // Insert inside accounts group? Or just global? Resource names are namespaced manually above.
        // Let's just append to end for safety
        file_put_contents($routesPath, $route, FILE_APPEND);
        echo "6. Route appended.\n";
    } else {
        file_put_contents($routesPath, $route, FILE_APPEND);
        echo "6. Route appended.\n";
    }
    Artisan::call('route:clear');
} else {
    echo "6. Route already exists.\n";
}

// 7. Update Sidebar
$sidebarPath = __DIR__ . '/../resources/views/layouts/sidebars/default.blade.php';
$sidebarContent = file_get_contents($sidebarPath);
if (!str_contains($sidebarContent, 'accounts.petty-cash.index')) {
    $search = "{{ __('Bank Reconciliation') }}\n        </a>";
    $insert = $search . "\n        <a href=\"{{ route('accounts.petty-cash.index') }}\" class=\"flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.petty-cash.*') ? 'bg-accent text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-marine-800 hover:text-gray-900 dark:hover:text-white' }}\">\n            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"></path></svg>{{ __('Petty Cash') }}\n        </a>";
    
    $sidebarContent = str_replace($search, $insert, $sidebarContent);
    file_put_contents($sidebarPath, $sidebarContent);
    echo "7. Sidebar Updated.\n";
} else {
    echo "7. Sidebar already has Petty Cash.\n";
}

echo "Installation Complete!\n";
