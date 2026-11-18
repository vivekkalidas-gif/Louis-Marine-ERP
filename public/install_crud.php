<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Route;

// 1. Create Receipt View
$receiptContent = <<<'EOD'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt</title>
    <style>
        body { font-family: sans-serif; padding: 20px; text-align: center; }
        .receipt { max-width: 400px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; }
        .header { margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .details { text-align: left; margin-bottom: 30px; }
        .amount { font-size: 24px; font-weight: bold; margin: 20px 0; }
        .signature { margin-top: 50px; text-align: left; }
        .line { border-bottom: 1px solid #000; display: inline-block; width: 200px; margin-top: 50px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="header">
            <h2>{{ config('app.name') }}</h2>
            <p>Payment Voucher / Receipt</p>
        </div>
        
        <div class="details">
            <p><strong>Date:</strong> {{ $entry->date->format('d M Y') }}</p>
            <p><strong>Staff:</strong> {{ $entry->user->name }}</p>
            <p><strong>Transaction:</strong> {{ ucwords(str_replace('_', ' ', $entry->type)) }}</p>
            <p><strong>Description:</strong> {{ $entry->description }}</p>
        </div>

        <div class="amount">
            {{ number_format($entry->amount, 2) }}
        </div>

        <div class="signature">
            <p>Received / Paid by:</p>
            <div class="line"></div>
            <p>Signature</p>
        </div>

        <button class="no-print" onclick="window.print()" style="margin-top:20px; padding:10px 20px; cursor:pointer;">Print Receipt</button>
    </div>
</body>
</html>
EOD;
@mkdir(__DIR__ . '/../resources/views/admin/staff/ledger', 0755, true);
file_put_contents(__DIR__ . '/../resources/views/admin/staff/ledger/receipt.blade.php', $receiptContent);
echo "Created Receipt View.\n";

// 2. Update Controller
$controllerContent = <<<'EOD'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmployeeLedgerEntry;
use App\Models\PayrollEntry;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::whereHas('employeeDetail')
            ->with(['roles', 'employeeDetail'])
            ->get();  
        return view('admin.staff.index', compact('staff'));
    }

    public function show(User $user)
    {
        // View handles data loading explicitly now via fix_view logic mostly,
        // but we keep this for standard behavior
        $user->load(['employeeDetail', 'roles']);
        
        $ledger = \App\Models\EmployeeLedgerEntry::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        $balance = $ledger->sum('debit') - $ledger->sum('credit');

        $payrolls = \App\Models\PayrollEntry::where('user_id', $user->id)
            ->with('payroll')
            ->get()
            ->sortByDesc(function($entry) {
                return $entry->payroll->month ?? '';
            });

        return view('admin.staff.show', compact('user', 'ledger', 'balance', 'payrolls'));
    }

    public function storeData(Request $request, User $user)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:advance,repayment',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
        ]);

        EmployeeLedgerEntry::create([
            'user_id' => $user->id,
            'date' => $request->date,
            'type' => $request->type === 'advance' ? 'advance_given' : 'manual_repayment',
            'debit' => $request->type === 'advance' ? $request->amount : 0,
            'credit' => $request->type === 'repayment' ? $request->amount : 0,
            'description' => $request->description,
            'amount' => $request->amount,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Entry recorded successfully.');
    }

    // NEW METHODS FOR CRUD & PRINT

    public function updateLedgerEntry(Request $request, $id)
    {
        $entry = EmployeeLedgerEntry::findOrFail($id);
        
        $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string',
        ]);

        // Recalculate debit/credit based on original type
        $isAdvance = in_array($entry->type, ['advance_given', 'advance']);
        
        $entry->update([
            'date' => $request->date,
            'amount' => $request->amount,
            'debit' => $isAdvance ? $request->amount : 0,
            'credit' => !$isAdvance ? $request->amount : 0,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Entry updated successfully.');
    }

    public function destroyLedgerEntry($id)
    {
        $entry = EmployeeLedgerEntry::findOrFail($id);
        $entry->delete();
        return back()->with('success', 'Entry deleted successfully.');
    }

    public function printLedgerEntry($id)
    {
        $entry = EmployeeLedgerEntry::with('user')->findOrFail($id);
        return view('admin.staff.ledger.receipt', compact('entry'));
    }
}
EOD;
file_put_contents(__DIR__ . '/../app/Http/Controllers/Admin/StaffController.php', $controllerContent);
echo "Updated StaffController.\n";

// 3. Append Routes (Carefully)
$routesPath = __DIR__ . '/../routes/web.php';
$routesContent = file_get_contents($routesPath);

if (!str_contains($routesContent, 'destroyLedgerEntry')) {
    $newRoutes = "\n" . <<<'EOD'
    // Staff Ledger CRUD & Print
    Route::put('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'updateLedgerEntry'])->name('staff.ledger.update');
    Route::delete('staff/ledger/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroyLedgerEntry'])->name('staff.ledger.destroy');
    Route::get('staff/ledger/{id}/print', [\App\Http\Controllers\Admin\StaffController::class, 'printLedgerEntry'])->name('staff.ledger.print');
EOD;
    
    // Insert before the Resource route to ensure precedence, or just append to end of file if within a group?
    // Safer to append to the end of the file, assuming it ends with a closing group?
    // Actually, web.php usually ends with just closing brackets.
    // Let's replace the existing Resource definition with the Expanded definitions to be safe.
    
    $search = "Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);";
    $replace = $newRoutes . "\n    " . $search;
    
    if (str_contains($routesContent, $search)) {
        $routesContent = str_replace($search, $replace, $routesContent);
        file_put_contents($routesPath, $routesContent);
        echo "Routes Updated.\n";
    } else {
        echo "Could not find resource route anchor. Appending to end.\n";
        file_put_contents($routesPath, $newRoutes, FILE_APPEND);
    }
} else {
    echo "Routes already exist.\n";
}

// 4. Clear Route Cache
echo "Clearing Route Cache...\n";
\Illuminate\Support\Facades\Artisan::call('route:clear');
\Illuminate\Support\Facades\Artisan::call('view:clear');
