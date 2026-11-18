<?php
$file = __DIR__ . '/../app/Http/Controllers/Admin/Accounts/PettyCashController.php';

$controllerContent = <<<'EOD'
<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use App\Models\PettyCashEntry;
use App\Models\User;
use Illuminate\Http\Request;

class PettyCashController extends Controller
{
    public function index()
    {
        $entries = PettyCashEntry::orderBy('date', 'desc')->get();
        
        $totalDeposits = PettyCashEntry::where('type', 'deposit')->sum('amount');
        $totalExpenses = PettyCashEntry::where('type', 'expense')->sum('amount');
        $balance = $totalDeposits - $totalExpenses;
        
        // All staff for "Received By" dropdown - including technicians
        $staff = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'accountant', 'storekeeper', 'engineer', 'manager', 'technician']);
        })->orderBy('name')->get();
        
        // Only accountants and admins for "Approved By" dropdown
        $approvers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'accountant', 'manager']);
        })->orderBy('name')->get();
        
        return view('admin.accounts.petty_cash.index', compact('entries', 'balance', 'totalDeposits', 'totalExpenses', 'staff', 'approvers'));
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

file_put_contents($file, $controllerContent);
echo "<p style='color:green'>✓ Controller updated - technicians added to staff list</p>";
