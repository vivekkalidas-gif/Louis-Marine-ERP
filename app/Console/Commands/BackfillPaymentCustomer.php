<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;

class BackfillPaymentCustomer extends Command
{
    protected $signature = 'fix:payment-customer';
    protected $description = 'Backfill customer_id in payments from invoices';

    public function handle()
    {
        $this->info('Starting backfill...');
        $payments = Payment::with('invoice')->whereNull('customer_id')->get();
        
        $count = 0;
        foreach($payments as $payment) {
            if ($payment->invoice) {
                $payment->customer_id = $payment->invoice->customer_id;
                $payment->save();
                $count++;
            }
        }
        
        $this->info("Updated {$count} payments.");
    }
}
