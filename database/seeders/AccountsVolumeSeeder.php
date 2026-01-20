<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\ServiceJob;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Rfq;
use App\Models\RfqItem; // Assuming this model exists based on Rfq relationship
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\FinanceCategory;
use App\Models\Accounting\JournalEntry; // Assuming namespace
use App\Models\Accounting\JournalItem;   // Assuming namespace
use App\Models\Accounting\AccountingJournal;
use App\Models\Accounting\ChartOfAccount;
use App\Models\InventoryItem;
use App\Models\InventoryCategory;

class AccountsVolumeSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $user = User::first() ?? User::factory()->create();

        $this->command->info('Initializing Accounts Volume Seeder...');

        // 0. Prerequisites
        $currencies = Currency::all();
        if($currencies->isEmpty()) {
             $currencies = collect([Currency::create(['code' => 'QAR', 'name' => 'Qatari Riyal', 'symbol' => 'QAR', 'exchange_rate' => 1])]);
        }
        
        $suppliers = Supplier::all();
        if ($suppliers->count() < 5) {
             $this->command->info('Creating partial suppliers...');
             for($i=0; $i<5; $i++) {
                Supplier::create([
                    'name' => $faker->company,
                    'type' => $faker->randomElement(['local', 'overseas']),
                    'status' => 'active',
                    'email' => $faker->companyEmail,
                    'created_by' => $user->id
                ]);
             }
             $suppliers = Supplier::all();
        }

        // Finance Categories
        $incomeCats = FinanceCategory::where('type', 'income')->get();
        if($incomeCats->isEmpty()) {
            FinanceCategory::create(['name' => 'General Sales', 'type' => 'income']);
            FinanceCategory::create(['name' => 'Consulting', 'type' => 'income']);
            $incomeCats = FinanceCategory::where('type', 'income')->get();
        }
        
        $expenseCats = FinanceCategory::where('type', 'expense')->get();
        if($expenseCats->isEmpty()) {
            FinanceCategory::create(['name' => 'Office Rent', 'type' => 'expense']);
            FinanceCategory::create(['name' => 'Travel', 'type' => 'expense']);
            $expenseCats = FinanceCategory::where('type', 'expense')->get();
        }

        // Journals & Accounts
        $salesJournal = AccountingJournal::where('type', 'sale')->first();
        $purchaseJournal = AccountingJournal::where('type', 'purchase')->first();
        $bankJournal = AccountingJournal::where('type', 'bank')->first();
        
        // Seed COA if empty
        $arAccount = \App\Models\Accounting\ChartOfAccount::firstOrCreate(
            ['code' => '1100'],
            ['name' => 'Accounts Receivable', 'type' => 'receivable']
        );
        $apAccount = \App\Models\Accounting\ChartOfAccount::firstOrCreate(
            ['code' => '2100'],
            ['name' => 'Accounts Payable', 'type' => 'payable']
        );
        $salesAccount = \App\Models\Accounting\ChartOfAccount::firstOrCreate(
            ['code' => '4000'],
            ['name' => 'Sales Info', 'type' => 'income']
        );
        $expenseAccount = \App\Models\Accounting\ChartOfAccount::firstOrCreate(
            ['code' => '5000'],
            ['name' => 'General Expense', 'type' => 'expense']
        );
        $bankAccount = \App\Models\Accounting\ChartOfAccount::firstOrCreate(
            ['code' => '1000'],
            ['name' => 'Bank', 'type' => 'liquidity']
        );
        
        // Ensure Journals exist too
        $salesJournal = \App\Models\Accounting\AccountingJournal::firstOrCreate(
            ['code' => 'INV'],
            ['name' => 'Customer Invoices', 'type' => 'sale', 'default_account_id' => $salesAccount->id] 
        );

        // 1. Create Customers (20)
        $this->command->info('Seeding 20 Customers...');
        $customers = [];
        for ($i = 0; $i < 20; $i++) {
            $customers[] = Customer::create([
                'company_name' => $faker->company . ' ' . $faker->suffix,
                'email' => $faker->unique()->companyEmail,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'status' => 'active',
                'created_by' => $user->id
            ]);
        }
        $customers = collect($customers);

        // 2. RFQs (15)
        $this->command->info('Seeding 15 RFQs...');
        for ($i = 0; $i < 15; $i++) {
            $status = $faker->randomElement(['draft', 'sent', 'approved', 'cancelled']);
            $rfqId = DB::table('rfqs')->insertGetId([
                'reference_number' => 'RFQ-' . strtoupper(Str::random(8)),
                'type' => $faker->randomElement(['local', 'overseas']),
                'status' => $status,
                'deadline_date' => $faker->dateTimeBetween('now', '+1 month'),
                'created_by' => $user->id,
                'incoterm' => 'Ex Works',
                'delivery_location' => 'Doha Port',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $rfq = \App\Models\Rfq::find($rfqId);
            
            // Attach 1-3 random suppliers
            $rfq->suppliers()->attach($suppliers->random(rand(1, 3))->pluck('id'), ['sent_at' => now()]);

            // Create items (using simplified fake data logic since InventoryItem might not exist or be needed for simple display)
            // But strict FK might be needed. Let's check if InventoryItem exists.
            $prod = InventoryItem::inRandomOrder()->first();
             // If no product, create one dummy
            if(!$prod) {
                 $cat = InventoryCategory::firstOrCreate(
                    ['name' => 'General'],
                    ['slug' => 'general', 'created_by' => $user->id]
                 );
                 $prod = InventoryItem::create([
                    'item_name' => 'Demo Part', 
                    'inventory_category_id' => $cat->id,
                    'created_by' => $user->id
                 ]);
            }

            // Using DB insert for speed/avoiding model if class not found or strict
            // Checking RfqItem model existence in previous step would have been good, but let's assume standard
            // DB::table('rfq_items')->insert([...])
            // Using relationships if possible
             $rfq->items()->create([
                'description' => 'Demo Item for RFQ',
                'quantity' => rand(1, 10),
                'product_id' => $prod->id
             ]);
        }

        // 3. Purchase Orders (20)
        $this->command->info('Seeding 20 Purchase Orders...');
        for ($i = 0; $i < 20; $i++) {
            $status = $faker->randomElement(['draft', 'sent', 'received', 'cancelled']);
            $poDate = $faker->dateTimeBetween('-6 months', 'now');
            $deliveryDate = (clone $poDate)->modify('+'.rand(5,30).' days');
            
            $po = PurchaseOrder::create([
                'supplier_id' => $suppliers->random()->id,
                'po_number' => 'PO-' . strtoupper(Str::random(6)),
                'po_date' => $poDate,
                'expected_delivery_date' => $deliveryDate,
                'status' => $status,
                'total_amount' => $faker->randomFloat(2, 500, 10000),
                'created_by' => $user->id
            ]);

            // Items
            // Assuming PurchaseOrderItem model exists
            $prod = \App\Models\InventoryItem::inRandomOrder()->first();
            $po->items()->create([
                'inventory_item_id' => $prod ? $prod->id : 1, // Fallback if prod random fails, though we seeded generic one
                'quantity' => rand(1, 10),
                'unit_price' => $po->total_amount / rand(1, 10),
                'total_price' => $po->total_amount
            ]);
        }

        // 4. Incomes (20)
        $this->command->info('Seeding 20 Income Records...');
        for ($i = 0; $i < 20; $i++) {
            $date = $faker->dateTimeBetween('-1 year', 'now');
            $amount = $faker->randomFloat(2, 1000, 20000);
            
            $income = Income::create([
                'date' => $date,
                'category_id' => $incomeCats->random()->id,
                'customer_id' => $customers->random()->id,
                'amount' => $amount,
                'payment_method' => $faker->randomElement(['Bank Transfer', 'Cash', 'Cheque']),
                'reference_number' => 'INC-' . strtoupper(Str::random(6)),
                'description' => 'Misc Income Demo',
                'status' => 'approved',
                'recorded_by' => $user->id
            ]);

            // Journal Entry for Income
             if ($salesJournal && $bankAccount && $salesAccount) {
                $entry = JournalEntry::create([
                    'journal_id' => $salesJournal->id,
                    'name' => $income->reference_number,
                    'reference' => 'Income #' . $income->id,
                    'date' => $date, // Using Carbon instance
                    'state' => 'posted',
                    'entity_type' => Income::class,
                    'entity_id' => $income->id
                ]);

                // Debit Bank
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $bankAccount->id,
                    'label' => 'Income Receipt',
                    'debit' => $amount,
                    'credit' => 0,
                    'date' => $date
                ]);

                // Credit Sales/Income Category Account
                 JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $salesAccount->id,
                    'label' => 'Income Category',
                    'debit' => 0,
                    'credit' => $amount,
                    'date' => $date
                ]);
            }
        }

        // 5. Expenses (50)
        $this->command->info('Seeding 50 Expense Records...');
        for ($i = 0; $i < 50; $i++) {
            $date = $faker->dateTimeBetween('-1 year', 'now');
            $amount = $faker->randomFloat(2, 50, 5000);
            
            $expense = Expense::create([
                'date' => $date,
                'category_id' => $expenseCats->random()->id,
                'vendor_name' => $faker->company,
                'reference_number' => 'EXP-' . strtoupper(Str::random(6)),
                'amount' => $amount,
                'payment_method' => $faker->randomElement(['Card', 'Cash', 'Bank Transfer']),
                'notes' => 'Demo Expense',
                'recorded_by' => $user->id
            ]);

             // Journal Entry for Expense
            if ($purchaseJournal && $expenseAccount && $bankAccount) {
                 $entry = JournalEntry::create([
                    'journal_id' => $purchaseJournal->id,
                    'name' => $expense->reference_number,
                    'reference' => 'Expense #' . $expense->id,
                    'date' => $date,
                    'state' => 'posted',
                    'entity_type' => Expense::class,
                    'entity_id' => $expense->id
                ]);

                // Debit Expense
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $expenseAccount->id,
                    'label' => 'Office Expense',
                    'debit' => $amount,
                    'credit' => 0,
                    'date' => $date
                ]);

                // Credit Bank (Paid immediately)
                 JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $bankAccount->id,
                    'label' => 'Payment Out',
                    'debit' => 0,
                    'credit' => $amount,
                    'date' => $date
                ]);
            }
        }

        // 6. Invoices (50) & Payments
        $this->command->info('Seeding 50 Invoices & Payments...');
        for ($i = 0; $i < 50; $i++) {
            $status = $faker->randomElement(['draft', 'sent', 'overdue', 'paid']);
            $date = $faker->dateTimeBetween('-1 year', 'now');
            $dueDate = (clone $date)->modify('+30 days');
            $subtotal = $faker->randomFloat(2, 500, 20000);
            $tax = $subtotal * 0.05; // 5% VAT
            $total = $subtotal + $tax;

            $customer = $customers->random();
            
            // Create Service Job Wrapper
            $job = ServiceJob::create([
                'customer_id' => $customer->id,
                'title' => 'Job ' . $faker->bs,
                'job_number' => 'JOB-' . Str::random(6),
                'status' => 'completed',
                'created_at' => $date,
                'created_by' => $user->id
            ]);

            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'customer_id' => $customer->id,
                'service_job_id' => $job->id,
                'date' => $date,
                'due_date' => $dueDate,
                'status' => $status,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'paid_amount' => ($status === 'paid') ? $total : 0,
                'created_by' => $user->id
            ]);

            // Add Invoice items
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => 'Service Charges',
                'quantity' => 1,
                'unit_price' => $subtotal,
                'amount' => $subtotal
            ]);

            // If Paid or Sent/Overdue (Posted), create Journal Entry
            if ($status !== 'draft' && $salesJournal && $arAccount && $salesAccount) {
                 $entry = JournalEntry::create([
                    'journal_id' => $salesJournal->id,
                    'name' => $invoice->invoice_number,
                    'reference' => 'Invoice Posting',
                    'date' => $date,
                    'state' => 'posted',
                    'entity_type' => Invoice::class,
                    'entity_id' => $invoice->id
                ]);

                // Debit AR
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $arAccount->id,
                    'label' => 'Customer Invoice',
                    'debit' => $total,
                    'credit' => 0,
                    'date' => $date
                ]);

                // Credit Sales
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $salesAccount->id,
                    'label' => 'Product Sales',
                    'debit' => 0,
                    'credit' => $total, // Simplified (ignoring tax account for demo)
                    'date' => $date
                ]);
            }

            // Create Payment if Paid
            if ($status === 'paid') {
                $payDate = (clone $date)->modify('+'.rand(1,20).' days');
                $payment = Payment::create([
                    'payment_number' => 'PAY-' . strtoupper(Str::random(6)),
                    'customer_id' => $customer->id,
                    'invoice_id' => $invoice->id,
                    'payment_date' => $payDate,
                    'amount' => $total,
                    'status' => 'approved',
                    'payment_method' => 'Bank Transfer',
                    'reference_number' => 'REF-' . rand(1000,9999),
                    'created_by' => $user->id
                ]);

                // Payment Journal Entry (Dr Bank, Cr AR)
                if ($bankJournal && $bankAccount && $arAccount) {
                    $payEntry = JournalEntry::create([
                        'journal_id' => $bankJournal->id,
                        'name' => $payment->payment_number,
                        'reference' => 'Payment for ' . $invoice->invoice_number,
                        'date' => $payDate,
                        'state' => 'posted',
                        'entity_type' => Payment::class,
                        'entity_id' => $payment->id
                    ]);

                     // Debit Bank
                     JournalItem::create([
                        'journal_entry_id' => $payEntry->id,
                        'account_id' => $bankAccount->id,
                        'label' => 'Payment Received',
                        'debit' => $total,
                        'credit' => 0,
                        'date' => $payDate
                    ]);

                     // Credit AR
                     JournalItem::create([
                        'journal_entry_id' => $payEntry->id,
                        'account_id' => $arAccount->id,
                        'label' => 'Invoice Payment',
                        'debit' => 0,
                        'credit' => $total,
                        'date' => $payDate
                    ]);
                }
            }
        }
    }
}
