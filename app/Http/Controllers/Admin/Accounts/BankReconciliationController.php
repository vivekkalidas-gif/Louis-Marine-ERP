<?php

namespace App\Http\Controllers\Admin\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\BankStatement;
use App\Models\Accounting\BankStatementLine;
use App\Models\Accounting\AccountingJournal;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalItem;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    /**
     * List all Bank Statements.
     */
    public function index()
    {
        $statements = BankStatement::with('journal')->latest('date')->paginate(20);
        return view('accounts.reconciliation.index', compact('statements'));
    }

    /**
     * Show form to create a new Bank Statement.
     */
    public function create()
    {
        $journals = AccountingJournal::whereIn('type', ['bank', 'cash'])->get();
        return view('accounts.reconciliation.create', compact('journals'));
    }

    /**
     * Store a new Bank Statement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'journal_id' => 'required|exists:accounting_journals,id',
            'balance_start' => 'required|numeric',
            'balance_end_real' => 'required|numeric',
        ]);

        $statement = BankStatement::create($request->all());

        return redirect()->route('accounts.reconciliation.show', $statement)->with('success', 'Statement Created');
    }

    /**
     * Show Statement Details & Reconciliation Interface.
     */
    /**
     * Show Statement Details & Reconciliation Interface.
     */
    public function show(BankStatement $statement)
    {
        $statement->load('lines.journalEntry');
        
        // Calculate computed ending balance
        $totalLines = $statement->lines->sum('amount');
        $computedEnd = $statement->balance_start + $totalLines;
        
        // Fetch Unreconciled Journal Items
        $journalItems = JournalItem::whereHas('entry', function($q) use ($statement) {
                $q->where('journal_id', $statement->journal_id)
                  ->where('state', 'posted')
                  ->doesntHave('bankStatementLines');
            })
            ->orderBy('date', 'desc')
            ->limit(200)
            ->get();

        return view('accounts.reconciliation.show', compact('statement', 'computedEnd', 'journalItems'));
    }

    /**
     * Auto-Match Action
     */
    public function autoMatch(BankStatement $statement, \App\Services\Accounting\BankReconciliationService $service)
    {
        $count = $service->autoMatch($statement);
        return back()->with('success', "Auto-matched {$count} transactions.");
    }

    /**
     * Store Lines (Transactions) to the Statement.
     */
    public function storeLine(Request $request, BankStatement $statement)
    {
        $request->validate([
            'date' => 'required|date',
            'label' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $statement->lines()->create($request->all());

        return back()->with('success', 'Line Added');
    }

    /**
     * Reconcile: match a Statement Line to an existing Journal Entry.
     */
    public function match(Request $request, BankStatement $statement, BankStatementLine $line, \App\Services\Accounting\BankReconciliationService $service)
    {
        $request->validate([
            'journal_entry_id' => 'required|exists:journal_entries,id',
        ]);

        $entry = JournalItem::where('entry_id', $request->journal_entry_id)->first(); // Just need entry_id really

        // Using service to handle match logic
        // Ideally we pass JournalItem if service expects it, or update service to accept ID.
        // My service expects JournalItem.
        // Let's find the specific Item that matches? 
        // For Manual match, we just want to link. 
        // Service::match($line, $item) -> uses item->entry_id.
        // So any item from that entry works for now.
        $item = JournalItem::where('entry_id', $request->journal_entry_id)->first();
        
        $service->match($line, $item);

        return back()->with('success', 'Transaction Matched');
    }
}
