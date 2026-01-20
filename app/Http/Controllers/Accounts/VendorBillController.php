<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingJournal;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use App\Models\Supplier;
use App\Models\VendorBill;
use App\Models\VendorBillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VendorBillController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorBill::with('vendor', 'creator')->latest();
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        $bills = $query->paginate(20);
        
        return view('accounts.vendor_bills.index', compact('bills'));
    }

    public function create()
    {
        $vendors = Supplier::all();
        $accounts = ChartOfAccount::whereIn('type', ['expense', 'cost_of_revenue', 'fixed_asset'])->get(); // Filter relevant accounts
        
        return view('accounts.vendor_bills.create', compact('vendors', 'accounts'));
    }

    public function createFromOrder(\App\Models\PurchaseOrder $purchaseOrder)
    {
        $vendors = Supplier::all();
        $accounts = ChartOfAccount::whereIn('type', ['expense', 'cost_of_revenue', 'fixed_asset'])->get();
        
        $prefilledData = [
            'vendor_id' => $purchaseOrder->supplier_id,
            'bill_number' => $purchaseOrder->po_number . '-INV', // Suggested
            'bill_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'), 
            'notes' => 'Converted from PO ' . $purchaseOrder->po_number,
            'purchase_order_id' => $purchaseOrder->id,
            'items' => []
        ];

        // Map PO Items to Bill Items
        // We need an expense account for each item. Logic:
        // 1. Try to find a default COGS/Expense account from Settings?
        // 2. Or just grab the first 'cost_of_revenue' account as logic fallback.
        // 3. Or use the request input to let user choose.
        // For pre-fill, let's try to find a reasonable default account.
        
        $defaultAccount = ChartOfAccount::where('type', 'cost_of_revenue')->first() ?? ChartOfAccount::where('type', 'expense')->first();

        foreach ($purchaseOrder->items as $item) {
            $prefilledData['items'][] = [
                'inventory_item_id' => $item->inventory_item_id,
                'description' => $item->inventoryItem->item_name ?? 'Item',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => 0,
                'total' => $item->quantity * $item->unit_price,
                'account_id' => $defaultAccount ? $defaultAccount->id : null,
            ];
        }

        return view('accounts.vendor_bills.create', compact('vendors', 'accounts', 'prefilledData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:suppliers,id',
            'bill_number' => 'required|string|max:255',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        
        // Uniqueness check for vendor + bill number
        if (VendorBill::where('vendor_id', $request->vendor_id)->where('bill_number', $request->bill_number)->exists()) {
             throw ValidationException::withMessages(['bill_number' => 'Check bill number (already exists for this vendor).']);
        }

        DB::transaction(function () use ($request) {
            $bill = VendorBill::create([
                'vendor_id' => $request->vendor_id,
                'bill_number' => $request->bill_number,
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'status' => 'draft',
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'purchase_order_id' => $request->purchase_order_id ?? null,
            ]);

            $subTotal = 0;
            $taxTotal = 0;

            foreach ($request->items as $item) {
                $qty = $item['quantity'];
                $price = $item['unit_price'];
                $taxRate = $item['tax_rate'] ?? 0;
                
                $lineTotalExcludeTax = $qty * $price;
                $lineTax = $lineTotalExcludeTax * ($taxRate / 100);
                $lineTotal = $lineTotalExcludeTax + $lineTax;

                $bill->items()->create([
                    'account_id' => $item['account_id'],
                    'description' => $item['description'] ?? 'Bill Item',
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $lineTax,
                    'total' => $lineTotal,
                    'inventory_item_id' => $item['inventory_item_id'] ?? null,
                ]);

                $subTotal += $lineTotalExcludeTax;
                $taxTotal += $lineTax;
            }

            $bill->update([
                'sub_total' => $subTotal,
                'tax_total' => $taxTotal,
                'total_amount' => $subTotal + $taxTotal
            ]);
        });

        return redirect()->route('accounts.vendor-bills.index')->with('success', 'Vendor Bill created.');
    }

    public function show(VendorBill $vendorBill)
    {
        $vendorBill->load('items.account', 'items.inventoryItem', 'vendor', 'journalEntry');
        return view('accounts.vendor_bills.show', compact('vendorBill'));
    }

    public function post(VendorBill $vendorBill)
    {
        if ($vendorBill->status !== 'draft') {
             return back()->with('error', 'Only draft bills can be posted.');
        }

        DB::transaction(function () use ($vendorBill) {
            // 1. Create Journal Entry
            // Find Purchase Journal
            $journal = AccountingJournal::where('type', 'purchase')->first();
            if (!$journal) {
                // Fallback or create?
                // throw new \Exception("Purchase Journal not found.");
                 // Assuming ID exists or we need to seed it. 
                 // For now, let's grab the first journal or a specific one.
                 // Ideally, we should have 'Purchase' journal seeded.
                 $journalId = $journal ? $journal->id : AccountingJournal::first()->id; 
            } else {
                $journalId = $journal->id;
            }
            
            // Generate Number: BILL/2026/001 ? Or use Bill Number as Ref
            $jeNumber = 'BILL/' . date('Y/m/') . $vendorBill->bill_number; 

            $entry = JournalEntry::create([
                'journal_id' => $journalId,
                'date' => $vendorBill->bill_date, // Accounting date = Bill Date? Or Posting Date?
                'name' => $jeNumber,
                'reference' => $vendorBill->bill_number,
                'state' => 'posted', // Auto-post
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'entity_id' => $vendorBill->id,
                'entity_type' => VendorBill::class,
            ]);

            // 2. Journal Items
            // Debit Expenses (from Items)
            // Credit Accounts Payable (Total)
            
            // Group expenses by account to condense lines? No, keep detail
            foreach ($vendorBill->items as $item) {
                $entry->items()->create([
                    'account_id' => $item->account_id,
                    'debit' => $item->total, // Expense includes tax if not recoverable? 
                    // Assume tax is expense for now (gross), or separate tax logic later.
                    // If tax is separate, we need VAT Account. 
                    // Simple MVP: Total to Expense.
                    // Ideally: Dr Expense (Subtotal), Dr VAT (Tax), Cr AP (Total).
                    // Let's split if tax > 0? 
                    // For now, just put total to expense account to keep it simple unless Tax Account setup exists.
                    'credit' => 0,
                    'label' => $item->description, // Corrected column name
                    'partner_id' => $vendorBill->vendor_id,
                    'date' => $vendorBill->bill_date,
                ]);
            }
            
            // Credit AP
            // Find AP Account (from Supplier or Settings)
            // Default AP account? 210000? 
            // We need a setting or logic to find "Accounts Payable" account.
            $apAccount = ChartOfAccount::where('type', 'payable')->first(); 
            if (!$apAccount) {
                 throw new \Exception("Accounts Payable account not found.");
            }

            $entry->items()->create([
                'account_id' => $apAccount->id,
                'debit' => 0,
                'credit' => $vendorBill->total_amount,
                'label' => 'Vendor Bill: ' . $vendorBill->bill_number, // Corrected column name
                'partner_id' => $vendorBill->vendor_id,
                'date' => $vendorBill->bill_date,
            ]);
            
            // Update Bill
            $vendorBill->update([
                'status' => 'posted', 
                'posted_by' => auth()->id(),
                'journal_entry_id' => $entry->id
            ]);
        });

        return back()->with('success', 'Bill posted successfully.');
    }
}
