<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\AccountingJournal;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JournalEntryController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with(['journal', 'creator', 'poster', 'items'])
            ->orderBy('date', 'desc')
            ->orderBy('number', 'desc')
            ->paginate(20);

        return view('accounts.journals.index', compact('entries'));
    }

    public function create()
    {
        $journals = AccountingJournal::all();
        $accounts = ChartOfAccount::orderBy('code')->get();
        $costCenters = CostCenter::orderBy('code')->get();
        
        // Generate next provisional number? Or just leave JS to handle temporary ID
        return view('accounts.journals.create', compact('journals', 'accounts', 'costCenters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'journal_id' => 'required|exists:accounting_journals,id',
            'reference' => 'nullable|string|max:255',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'required|numeric|min:0',
            'items.*.credit' => 'required|numeric|min:0',
            'items.*.cost_center_id' => 'nullable|exists:cost_centers,id',
        ]);

        // Validation: Sum of Debit = Sum of Credit
        $totalDebit = collect($request->items)->sum('debit');
        $totalCredit = collect($request->items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
             return back()->withErrors(['items' => 'Journal Entry is not balanced. Difference: ' . ($totalDebit - $totalCredit)]);
        }

        $this->checkPeriodLock($request->date);

        DB::transaction(function () use ($request) {
            // 1. Create Header
            $journal = AccountingJournal::find($request->journal_id);
            // Auto-generate Number logic (Simplified for now, e.g., JRNL-YYYY-MM-XXXX)
            $number = $journal->code . '/' . date('Y/m/') . uniqid(); 

            $entry = JournalEntry::create([
                'journal_id' => $request->journal_id,
                'date' => $request->date,
                'number' => $number,
                'reference' => $request->reference,
                'state' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // 2. Create Items
            foreach ($request->items as $item) {
                $entry->items()->create([
                    'account_id' => $item['account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'label' => $item['label'] ?? null,
                    'cost_center_id' => $item['cost_center_id'] ?? null,
                    'date' => $request->date, // Denormalized date
                ]);
            }
        });

        return redirect()->route('accounts.journals.index')->with('success', 'Journal Entry created successfully.');
    }

    public function edit(JournalEntry $journalEntry)
    {
        if ($journalEntry->state === 'posted') {
            return redirect()->route('accounts.journals.show', $journalEntry)->with('error', 'Posted entries cannot be edited.');
        }

        $journals = AccountingJournal::all();
        $accounts = ChartOfAccount::orderBy('code')->get();
        $costCenters = CostCenter::orderBy('code')->get();
        $journalEntry->load('items');

        return view('accounts.journals.edit', compact('journalEntry', 'journals', 'accounts', 'costCenters'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->state === 'posted') {
             abort(403, 'Cannot edit posted entry');
        }

        $this->checkPeriodLock($journalEntry->date); // Check if OLD date is locked
        $this->checkPeriodLock($request->date);      // Check if NEW date is locked

        // ... Similar validation and update logic ...
        // Re-create items or update existing? Easier to delete all and recreate for keeping integrity.
        // For audit logs, updating existing is better but complex logic. 
        // Let's delete and recreating for MVP MVP.
        
        DB::transaction(function () use ($request, $journalEntry) {
             $journalEntry->update([
                 'date' => $request->date,
                 'reference' => $request->reference,
                 // Journal ID change might change number sequence... prevent for now.
             ]);
             
             $journalEntry->items()->delete(); // Soft delete old items
             
             foreach ($request->items as $item) {
                $journalEntry->items()->create([
                    'account_id' => $item['account_id'],
                    'debit' => $item['debit'],
                    'credit' => $item['credit'],
                    'label' => $item['label'] ?? null,
                    'cost_center_id' => $item['cost_center_id'] ?? null,
                    'date' => $request->date,
                ]);
            }
        });

        return redirect()->route('accounts.journals.index')->with('success', 'Journal Entry updated.');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['items.account', 'items.costCenter', 'journal', 'creator', 'poster']);
        return view('accounts.journals.show', compact('journalEntry'));
    }


    public function post(JournalEntry $journalEntry)
    {
         $this->checkPeriodLock($journalEntry->date);

         // Validation: Balanced?
         if (!$journalEntry->isBalanced()) {
             return back()->with('error', 'Entry is not balanced.');
         }
         
         $journalEntry->update([
             'state' => 'posted',
             'posted_by' => auth()->id()
         ]);
         
         return back()->with('success', 'Journal Entry posted successfully.');
    }

    /**
     * Reverse a Posted Journal Entry
     */
    public function reverse(Request $request, JournalEntry $journalEntry)
    {
        // 1. Validation
        if ($journalEntry->state !== 'posted') {
            return back()->with('error', 'Only posted entries can be reversed.');
        }
        if ($journalEntry->reversed_entry_id) {
             return back()->with('error', 'This entry is already reversed.');
        }

        $request->validate([
            'date' => 'required|date',
            'reason' => 'required|string|max:1000',
        ]);

        $this->checkPeriodLock($request->date);

        DB::transaction(function() use ($request, $journalEntry) {
            
            // 2. Create Reversal Header
            $reversal = JournalEntry::create([
                'journal_id' => $journalEntry->journal_id,
                'date' => $request->date,
                'number' => $journalEntry->journal->code . '/' . date('Y/m/') . uniqid() . '-REV',
                'reference' => 'Reversal of ' . $journalEntry->number,
                'state' => 'posted', // Reversals are auto-posted
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'reversal_reason' => $request->reason,
                'reversed_entry_id' => $journalEntry->id, 
                // We link the NEW entry to the OLD one via reversed_entry_id?
                // Or vice versa?
                // Requirements: "From original... View linked reversal".
                // "From reversal... Navigate back to original".
                // schema: reversed_entry_id on `journal_entries` table.
                // Let's say Entry A is reversed by Entry B.
                // Entry A.reversed_entry_id = Entry B.id?
                // Entry B.reversed_entry_id = Entry A.id?
                // Usually: Entry B (Reversal) points to Entry A (Original).
                // So Entry B.reversed_entry_id = Entry A.id.
                // update Entry A to say "I am reversed". Maybe a flag or checking relation.
                // Let's store Entry A.id in Entry B.reversed_entry_id.
            ]);
            
            // 3. Create Items (Swap Debit/Credit)
            foreach ($journalEntry->items as $item) {
                $reversal->items()->create([
                    'account_id' => $item->account_id,
                    'debit' => $item->credit, // Swap
                    'credit' => $item->debit, // Swap
                    'label' => 'Reversal: ' . $item->label,
                    'cost_center_id' => $item->cost_center_id,
                    'date' => $request->date,
                    'partner_id' => $item->partner_id,
                ]);
            }
            
            // 4. Update Original Entry
            // We need to know it was reversed.
            // If we use `reversed_entry_id` column on the NEW entry to point to OLD,
            // we might need another column/flag on OLD entry or just query.
            // But Odoo style: Original Entry status -> Reversed?
            // User req: "Original Entry -> Posted (Reversed)".
            // Let's update `reversed_entry_id` on the OLD entry to point to NEW entry.
            // Wait, cyclic?
            // Let's stick to: Original Entry (A) has `reversed_entry_id` = Reversal Entry (B).
            // Reversal Entry (B) has `reference` = "Reversal of A".
            // AND we can add `reversed_entry_id` on B pointing to A for bidirectional nav?
            // Schema has only 1 column `reversed_entry_id`.
            // Let's use it on the ORIGINAL entry.
            // Entry A (reversed_entry_id = B).
            // This allows us to easily see "This entry was reversed by B".
            
            $journalEntry->update([
               'reversed_entry_id' => $reversal->id,
               'reversed_date' => $request->date
            ]);
            // Reversal ID on the Reversal itself? No need if Ref string is enough.
            // But having structural link is better.
            // Since we can't put two IDs in one column, let's assume `reversed_entry_id` implies "This entry IS REVERSED BY X" (on original).
            
        });

        return back()->with('success', 'Entry reversed successfully.');
    }

    /**
     * Check if the date falls in a locked period.
     * Throws ValidationException if locked.
     */
    private function checkPeriodLock($date)
    {
        // Ensure date is Y-m-d format if it's a string, or Carbon object
        if ($date instanceof \Carbon\Carbon) {
            $date = $date->format('Y-m-d');
        }

        $period = \App\Models\Accounting\AccountingPeriod::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($period && $period->is_locked) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'date' => "The date {$date} falls within a locked accounting period ({$period->name}). Operations are not allowed."
            ]);
        }
    }
}
