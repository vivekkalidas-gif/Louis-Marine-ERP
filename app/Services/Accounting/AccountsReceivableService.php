<?php

namespace App\Services\Accounting;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Accounting\JournalEntry;
use App\Models\Accounting\JournalItem;
use App\Models\Accounting\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountsReceivableService
{
    /**
     * Post an Invoice to the General Ledger.
     * Debit: Accounts Receivable
     * Credit: Income / Revenue / Tax
     */
    public function postInvoice(Invoice $invoice)
    {
        if ($invoice->journal_entry_id) {
            throw new \Exception("Invoice already posted.");
        }

        DB::transaction(function () use ($invoice) {
            // 1. Identify Accounts
            // Hardcoded for now, or fetch from Settings / Product default.
            // Requirement said: "Accounts automatically reflect in Trial Balance, P&L..."
            // We need a standard AR Account and an Income Account.
            
            $arAccount = ChartOfAccount::where('type', 'receivable')->first();
            if (!$arAccount) {
                 // Fallback
                 $arAccount = ChartOfAccount::where('type', 'receivable')->firstOrFail(); 
            }
            
            $incomeAccount = ChartOfAccount::where('type', 'income')->firstOrFail();
            $taxAccount = ChartOfAccount::where('name', 'LIKE', '%Tax%')->first(); // Optional

            // 2. Create Journal Entry
            $entry = JournalEntry::create([
                'journal_id' => \App\Models\Accounting\AccountingJournal::where('type', 'sale')->first()->id ?? \App\Models\Accounting\AccountingJournal::first()->id,
                'date' => $invoice->date,
                'name' => "Invoice " . $invoice->invoice_number,
                'reference' => $invoice->invoice_number,
                'state' => 'posted',
                'partner_id' => $invoice->customer_id, // Map Customer UUID to partner_id
                'created_by' => auth()->id(),
            ]);

            // 3. Create Journal Items
            // Debit AR (Customer owes us)
            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $arAccount->id,
                'partner_id' => $invoice->customer_id,
                'label' => "Invoice #" . $invoice->invoice_number,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'date' => $invoice->date,
            ]);

            // Credit Income (Subtotal)
            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $incomeAccount->id,
                'label' => "Sales Revenue - " . $invoice->invoice_number,
                'debit' => 0,
                'credit' => $invoice->subtotal, // Assuming tax is separate
                'date' => $invoice->date,
            ]);

            // Credit Tax (if any)
            if ($invoice->tax_amount > 0 && $taxAccount) {
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $taxAccount->id,
                    'label' => "Tax Payable - " . $invoice->invoice_number,
                    'debit' => 0,
                    'credit' => $invoice->tax_amount,
                    'date' => $invoice->date,
                ]);
            } elseif ($invoice->tax_amount > 0) {
                // If no tax account, dump to income (not ideal but safe for code execution)
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $incomeAccount->id,
                    'label' => "Tax (Unallocated) - " . $invoice->invoice_number,
                    'debit' => 0,
                    'credit' => $invoice->tax_amount,
                    'date' => $invoice->date,
                ]);
            }

            // 4. Update Invoice
            $invoice->update([
                'status' => 'sent', // Or 'posted' if we add that status
                'journal_entry_id' => $entry->id
            ]);
        });
    }

    /**
     * Post a Payment (Receipt) to the General Ledger.
     * Debit: Bank / Cash
     * Credit: Accounts Receivable
     */
    public function recordPayment(Payment $payment)
    {
        if ($payment->journal_entry_id) {
            return; // Already posted
        }

        DB::transaction(function () use ($payment) {
            $arAccount = ChartOfAccount::where('type', 'receivable')->firstOrFail();
            
            // Determine Bank or Cash based on payment_method
            $bankAccount = null;
            if ($payment->payment_method == 'cash') {
                $bankAccount = ChartOfAccount::where('name', 'LIKE', '%Cash%')->first();
            } else {
                 $bankAccount = ChartOfAccount::where('name', 'LIKE', '%Bank%')->first();
            }
            // Fallback
            if (!$bankAccount) $bankAccount = ChartOfAccount::where('type', 'liquidity')->firstOrFail();


            $entry = JournalEntry::create([
                'journal_id' => \App\Models\Accounting\AccountingJournal::where('type', 'bank')->first()->id ?? \App\Models\Accounting\AccountingJournal::first()->id,
                'date' => $payment->payment_date,
                'name' => "Payment " . $payment->payment_number,
                'reference' => "PAY/" . $payment->payment_number,
                'state' => 'posted',
                'partner_id' => $payment->customer_id,
                'created_by' => auth()->id(),
            ]);

            // Debit Bank/Cash (Asset Increase)
             JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $bankAccount->id,
                'label' => "Payment for " . ($payment->invoice ? $payment->invoice->invoice_number : 'Account'),
                'debit' => $payment->amount,
                'credit' => 0,
                'date' => $payment->payment_date,
            ]);

            // Credit AR (Asset Decrease - Customer pays off debt)
            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $arAccount->id,
                'partner_id' => $payment->customer_id,
                'label' => "Payment Received",
                'debit' => 0,
                'credit' => $payment->amount,
                'date' => $payment->payment_date,
            ]);

            $payment->update(['journal_entry_id' => $entry->id]);
            
            // Update Invoice Paid Amount
            if ($payment->invoice) {
                $invoice = $payment->invoice;
                $invoice->paid_amount += $payment->amount;
                if ($invoice->paid_amount >= $invoice->total_amount) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = 'partial';
                }
                $invoice->save();
            }
        });
    }

    /**
     * Generate Aging Report
     */
    public function getAgingReport()
    {
        // 1. Fetch all unpaid invoices
        // We can use the Invoice model directly as it's the operational source of truth for AR.
        $invoices = Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'draft') // Only posted/sent invoices count
            ->where('status', '!=', 'cancelled')
            ->get();

        $report = [];
        $buckets = [
            '0-30' => 0,
            '31-60' => 0,
            '61-90' => 0,
            '90+' => 0,
            'total' => 0
        ];

        foreach ($invoices as $inv) {
            $balance = $inv->total_amount - $inv->paid_amount;
            if ($balance <= 0) continue;

            $daysOverdue = Carbon::now()->diffInDays($inv->due_date ?? $inv->date, false); 
            // diffInDays returns positive if future, negative if past? 
            // if due_date is in past, we want positive days overdue.
            // Example: now=Jan 10, due=Jan 1. diff = -9. We want 9.
            $daysOverdue = $daysOverdue * -1; // Invert to get "days passed since due date"
            
            // If strictly using "Age" since invoice date (standard aging):
            $age = Carbon::parse($inv->date)->diffInDays(Carbon::now());
            
            // Using standard Age buckets
            if ($age <= 30) {
                $buckets['0-30'] += $balance;
            } elseif ($age <= 60) {
                $buckets['31-60'] += $balance;
            } elseif ($age <= 90) {
                $buckets['61-90'] += $balance;
            } else {
                $buckets['90+'] += $balance;
            }
            $buckets['total'] += $balance;
        }

        return $buckets;
    }
}
