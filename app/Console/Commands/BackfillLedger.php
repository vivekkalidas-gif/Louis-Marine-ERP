<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\LedgerEntry;
use App\Services\LedgerService;

class BackfillLedger extends Command
{
    protected $signature = 'fix:ledger-backfill';
    protected $description = 'Reconstruct ledger entries from existing Invoices and Payments';

    public function handle()
    {
        if (!$this->confirm('This will wipe existing ledger entries and rebuild them. Continue?')) {
            return;
        }

        LedgerEntry::truncate();
        $service = new LedgerService();

        // 1. Invoices (Debit)
        // Only finalized invoices (status != draft OR has payments?) 
        // Logic: If it has payments, it MUST be valid. If status is sent/partial/paid, it's valid.
        // 1. Invoices (Debit)
        $invoices = Invoice::with('customer')->where('status', '!=', 'draft')->orWhere('paid_amount', '>', 0)->get();
        
        $this->info("Processing {$invoices->count()} invoices...");

        // We need to process EVERYTHING in chronological order to get running balance right.
        // So let's collect all events first.
        
        $events = collect();

        foreach($invoices as $inv) {
            if (!$inv->customer) {
                $this->warn("Skipping Invoice {$inv->invoice_number}: Customer not found.");
                continue;
            }
            $events->push([
                'date' => $inv->date->toDateString(), // Use invoice date
                'type' => 'invoice',
                'amount' => $inv->total_amount,
                'customer' => $inv->customer,
                'reference' => $inv,
                'desc' => 'Invoice generated - ' . $inv->invoice_number,
                'created_at' => $inv->created_at
            ]);
        }

        $payments = Payment::with(['customer', 'invoice.customer'])->get();
        $this->info("Processing {$payments->count()} payments...");
        
        foreach($payments as $pay) {
            $customer = $pay->customer;
            // Fallback to Invoice Customer
            if (!$customer && $pay->invoice && $pay->invoice->customer) {
                $customer = $pay->invoice->customer;
            }

            if (!$customer) {
                 $this->warn("Skipping Payment {$pay->payment_number}: No linked customer.");
                continue;
            }
            
            $events->push([
                'date' => $pay->payment_date->toDateString(),
                'type' => 'payment',
                'amount' => $pay->amount,
                'customer' => $customer,
                'reference' => $pay,
                'desc' => 'Payment received' . ($pay->reference_number ? " (Ref: {$pay->reference_number})" : ''),
                'created_at' => $pay->created_at
            ]);
        }
        
        // Sort by Date, then by Created At
        $sortedEvents = $events->sortBy(function($event) {
            return $event['date'] . $event['created_at'];
        });

        $bar = $this->output->createProgressBar($sortedEvents->count());

        foreach($sortedEvents as $event) {
            $service->recordEntry(
                $event['customer'],
                $event['type'],
                $event['amount'],
                $event['reference'],
                $event['desc'],
                $event['date']
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Ledger reconstruction complete.');
    }
}
