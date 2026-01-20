<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingJournal;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\JournalEntry;
use App\Models\Supplier;
use App\Models\VendorBill;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorPaymentController extends Controller
{
    public function index()
    {
        $payments = VendorPayment::with('vendor')->latest()->paginate(20);
        return view('accounts.vendor_payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $vendors = Supplier::all();
        $bankAccounts = ChartOfAccount::whereIn('type', ['liquidity', 'current_asset'])->get(); // Cash/Bank
        
        $selectedVendor = null;
        $unpaidBills = collect();
        if ($request->has('vendor_id')) {
            $selectedVendor = Supplier::find($request->vendor_id);
            if ($selectedVendor) {
                // Get bills that are posted and not fully paid
                // Not fully paid logic: status != 'paid'.
                // Ideally, we compare total_amount vs paid_amount
                // ScopePending helps?
                $unpaidBills = VendorBill::where('vendor_id', $selectedVendor->id)
                    ->whereIn('status', ['posted', 'partial'])
                    ->get()
                    ->filter(function($bill) {
                        return $bill->outstanding_amount > 0;
                    });
            }
        }

        return view('accounts.vendor_payments.create', compact('vendors', 'bankAccounts', 'selectedVendor', 'unpaidBills'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required|exists:suppliers,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.1',
            'bank_account_id' => 'required|exists:chart_of_accounts,id', // Paid FROM
            'allocation' => 'nullable|array', // [bill_id => amount]
        ]);
        
        // Auto-generate number
        $number = 'PAY/' . date('Y/') . uniqid(); 

        DB::transaction(function () use ($request, $number) {
            $payment = VendorPayment::create([
                'payment_number' => $number,
                'vendor_id' => $request->vendor_id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => 'bank', // Default for now
                'bank_account_id' => $request->bank_account_id,
                'reference' => $request->reference,
                'notes' => $request->notes,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Allocation
            if ($request->allocation) {
                foreach ($request->allocation as $billId => $amount) {
                    if ($amount > 0) {
                        $payment->bills()->attach($billId, ['amount' => $amount]);
                        
                        // Check if bill is fully paid
                        $bill = VendorBill::find($billId); // refresh
                        // if ($bill->outstanding_amount <= 0) ... -> Logic needs paid_amount to include THIS payment.
                        // Since we just attached, $bill->paid_amount (which calcs from db) should update?
                        // Depending on if payment is 'posted' or not.
                        // Currently draft payment shouldn't reduce outstanding?
                        // Or does it?
                        // Usually only POSTED payment reduces outstanding.
                    }
                }
            }
        });

        return redirect()->route('accounts.vendor-payments.index')->with('success', 'Payment recorded.');
    }
    
    public function show(VendorPayment $vendorPayment)
    {
        $vendorPayment->load('vendor', 'bills');
        return view('accounts.vendor_payments.show', compact('vendorPayment'));
    }

    public function post(VendorPayment $vendorPayment)
    {
        if ($vendorPayment->status !== 'draft') {
            return back()->with('error', 'Already posted.');
        }

        DB::transaction(function () use ($vendorPayment) {
            // 1. Create Journal Entry
            // Dr Accounts Payable
            // Cr Bank/Cash
            
            $journal = AccountingJournal::where('type', 'bank')->first() ?? AccountingJournal::where('type', 'cash')->first();
            // Fallback
             $journalId = $journal ? $journal->id : AccountingJournal::first()->id;

            $entry = JournalEntry::create([
                'journal_id' => $journalId,
                'date' => $vendorPayment->payment_date,
                'name' => $vendorPayment->payment_number,
                'reference' => $vendorPayment->reference,
                'state' => 'posted',
                'created_by' => auth()->id(),
                'posted_by' => auth()->id(),
                'entity_id' => $vendorPayment->id,
                'entity_type' => VendorPayment::class,
            ]);
            
            // Dr AP
            $apAccount = ChartOfAccount::where('type', 'payable')->firstOrFail();
            
            $entry->items()->create([
                'account_id' => $apAccount->id,
                'debit' => $vendorPayment->amount,
                'credit' => 0,
                'label' => 'Payment to ' . $vendorPayment->vendor->name,
                'partner_id' => $vendorPayment->vendor_id,
                'date' => $vendorPayment->payment_date,
            ]);
            
            // Cr Bank
            $entry->items()->create([
                'account_id' => $vendorPayment->bank_account_id,
                'debit' => 0,
                'credit' => $vendorPayment->amount,
                'label' => 'Payment Out',
                'date' => $vendorPayment->payment_date,
            ]);

            $vendorPayment->update([
                'status' => 'posted',
                'journal_entry_id' => $entry->id
            ]);
            
            // Update Bill Statuses
            foreach ($vendorPayment->bills as $bill) {
                // Check if Fully Paid
                // Now that payment is posted, recalculate bill status
                if ($bill->outstanding_amount <= 0.01) {
                    $bill->update(['status' => 'paid']);
                } else {
                    $bill->update(['status' => 'partial']);
                }
            }
        });
        
        return back()->with('success', 'Payment posted.');
    }
    public function edit(VendorPayment $vendorPayment)
    {
        if ($vendorPayment->status !== 'draft') {
            return redirect()->route('accounts.vendor-payments.show', $vendorPayment)->with('error', 'Cannot edit a posted payment.');
        }

        $vendors = Supplier::all();
        $bankAccounts = ChartOfAccount::whereIn('type', ['liquidity', 'current_asset'])->get();
        
        $selectedVendor = $vendorPayment->vendor;
        $unpaidBills = collect();

        // Get bills that are valid for allocation:
        // 1. Currently allocated to this payment (so we can adjust/keep them)
        // 2. Unpaid/Partial bills from this vendor
        
        $currentBillIds = $vendorPayment->bills->pluck('id')->toArray();
        
        $availableBills = VendorBill::where('vendor_id', $selectedVendor->id)
            ->whereIn('status', ['posted', 'partial'])
            ->get();
            
        // Merrge with currently allocated bills (even if they are now 'paid' fully by this payment logic in future, but for now they are just linked)
        // Actually, since it's draft, the bills might still be open. 
        // Just fetch all posted/partial bills and also include any bill that is already attached to this payment (in case it became 'paid' by another means? Unlikely if we are editing draft).
        
        $unpaidBills = $availableBills->filter(function($bill) use ($currentBillIds) {
             return $bill->outstanding_amount > 0 || in_array($bill->id, $currentBillIds);
        });

        return view('accounts.vendor_payments.edit', compact('vendorPayment', 'vendors', 'bankAccounts', 'selectedVendor', 'unpaidBills'));
    }

    public function update(Request $request, VendorPayment $vendorPayment)
    {
        if ($vendorPayment->status !== 'draft') abort(403);

        $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.1',
            'bank_account_id' => 'required|exists:chart_of_accounts,id',
            'allocation' => 'nullable|array',
            'notes' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $vendorPayment) {
            $vendorPayment->update([
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'bank_account_id' => $request->bank_account_id,
                'reference' => $request->reference,
                'notes' => $request->notes,
            ]);

            // Sync Allocation
            $syncData = [];
            if ($request->allocation) {
                foreach ($request->allocation as $billId => $amount) {
                    if ($amount > 0) {
                        $syncData[$billId] = ['amount' => $amount];
                    }
                }
            }
            $vendorPayment->bills()->sync($syncData);
        });

        return redirect()->route('accounts.vendor-payments.show', $vendorPayment)->with('success', 'Payment updated.');
    }

    public function destroy(VendorPayment $vendorPayment)
    {
        if ($vendorPayment->status !== 'draft') {
            return back()->with('error', 'Cannot delete a posted payment.');
        }

        $vendorPayment->bills()->detach(); // Pivot cleanup
        $vendorPayment->delete();

        return redirect()->route('accounts.vendor-payments.index')->with('success', 'Payment deleted.');
    }
    
    public function pdf(VendorPayment $vendorPayment)
    {
        $vendorPayment->load(['vendor', 'bills']);
        $settings = \App\Models\Setting::getAllCached();
        $company = [
            'name' => $settings['company_name'] ?? 'Louis Marine Equipment',
            'address' => $settings['company_address'] ?? 'Doha, Qatar',
            'phone' => $settings['company_phone'] ?? '',
            'email' => $settings['company_email'] ?? '',
            'trn' => $settings['company_trn'] ?? '',
            'logo' => $settings['company_logo'] ?? null,
        ];
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('accounts.vendor_payments.pdf', compact('vendorPayment', 'company'));
        $filename = 'payment_' . str_replace(['/', '\\'], '_', $vendorPayment->payment_number) . '.pdf';
        return $pdf->download($filename);
    }

    public function print(VendorPayment $vendorPayment)
    {
        $vendorPayment->load(['vendor', 'bills']);
        $settings = \App\Models\Setting::getAllCached();
        $company = [
            'name' => $settings['company_name'] ?? 'Louis Marine Equipment',
            'address' => $settings['company_address'] ?? 'Doha, Qatar',
            'phone' => $settings['company_phone'] ?? '',
            'email' => $settings['company_email'] ?? '',
            'trn' => $settings['company_trn'] ?? '',
            'logo' => $settings['company_logo'] ?? null,
        ];
         
        return view('accounts.vendor_payments.print', compact('vendorPayment', 'company'));
    }
}
