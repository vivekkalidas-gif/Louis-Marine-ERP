<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\ServiceJob;
use App\Models\Invoice;
use App\Models\Accounting\JournalEntry;
use App\Models\Rfq;
use App\Models\Storekeeper\RfqItem;
use App\Models\InventoryCategory;
use Illuminate\Support\Facades\DB;

class AccountingDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        // 1. Create Suppliers (Local & Overseas)
        $this->command->info('Creating Suppliers...');
        
        $localSupplier = Supplier::firstOrCreate(
            ['name' => 'Doha Industrial Supply'],
            [
                'type' => 'local',
                'status' => 'active',
                'category' => 'Trader',
                'email' => 'sales@dohaindustrial.qa',
                'phone' => '+974 4400 1234',
                'payment_terms' => '30 Days Net',
                'rating' => 4,
                'created_by' => $user->id
            ]
        );

        $overseasSupplier = Supplier::firstOrCreate(
            ['name' => 'Global Marine Parts (USA)'],
            [
                'type' => 'overseas',
                'status' => 'active',
                'category' => 'Manufacturer',
                'email' => 'orders@globalmarine.com',
                'currency_id' => Currency::where('code', 'USD')->first()->id ?? null,
                'incoterm' => 'Ex Works',
                'payment_terms' => 'Advance Payment',
                'rating' => 5,
                'created_by' => $user->id
            ]
        );

        // 2. Create RFQ & Quotations
        $this->command->info('Creating RFQ Flow...');
        
        $item = InventoryItem::firstOrCreate(
            ['item_name' => 'Test Hydraulic Pump'],
            [
                'inventory_category_id' => InventoryCategory::first()->id ?? InventoryCategory::create(['name' => 'Spares', 'code' => 'SPR'])->id,
                'current_stock' => 10,
                'brand' => 'HydrauCorp',
                'model' => 'HC-2000',
                'purchase_price' => 500,
                'selling_price' => 750,
                'unit' => 'Nos',
                'status' => 'active',
                'created_by' => $user->id
            ]
        );

        $rfq = Rfq::create([
            'reference_number' => 'RFQ/DEMO/' . rand(1000,9999),
            'type' => 'overseas',
            'status' => 'sent',
            'created_by' => $user->id,
            'deadline_date' => now()->addDays(7),
        ]);

        // Add Items
        DB::table('rfq_items')->insert([
            'rfq_id' => $rfq->id,
            'description' => 'Hydraulic Pump Assembly for testing',
            'quantity' => 2,
            'product_id' => $item->id,
            'created_at' => now(), 'updated_at' => now()
        ]);

        // Attach Suppliers
        $rfq->suppliers()->attach([$localSupplier->id, $overseasSupplier->id], ['sent_at' => now()]);

        // Add Quotations
        // Local Quote
        DB::table('supplier_quotations')->insert([
            'rfq_id' => $rfq->id,
            'supplier_id' => $localSupplier->id,
            'subtotal_amount' => 5000, // QAR
            'total_amount_foreign' => 5000,
            'exchange_rate' => 1,
            'total_amount_local' => 5000,
            'lead_time' => '3 Days',
            'is_selected' => false,
            'created_at' => now(), 'updated_at' => now()
        ]);

        // Overseas Quote
        DB::table('supplier_quotations')->insert([
            'rfq_id' => $rfq->id,
            'supplier_id' => $overseasSupplier->id,
            'subtotal_amount' => 1000, // USD
            'shipping_cost' => 200, // USD
            'customs_duty' => 50, // USD
            'total_amount_foreign' => 1250, // USD Total
            'exchange_rate' => 3.65, // QAR Rate
            'total_amount_local' => 1250 * 3.65, // ~4562.5 QAR
            'lead_time' => '14 Days',
            'is_selected' => true, // We pick this one
            'selection_note' => 'Better price despite longer lead time.',
            'created_at' => now(), 'updated_at' => now()
        ]);

        $rfq->update(['status' => 'approved']);

        
        // 3. Create Sales Flow (Invoice -> Payment -> Journal)
        $this->command->info('Creating Sales & Ledger Flow...');

        $customer = Customer::firstOrCreate(
            ['company_name' => 'Qatar Energy Demo'], 
            [
                'email' => 'finance@qe.qa', 
                'phone' => '44440000', 
                'vat_number' => '123456789'
            ]
        );

        $job = ServiceJob::create([
            'customer_id' => $customer->id,
            'title' => 'Emergency Repair - Demo',
            'job_number' => 'JOB-DEMO-' . rand(100,999),
            'status' => 'completed',
            'created_at' => now()->subDays(5)
        ]);

        // Create Invoice
        $invoice = Invoice::create([
            'service_job_id' => $job->id,
            'customer_id' => $customer->id, // Fixed: Added mandatory customer_id
            'invoice_number' => 'INV-DEMO-' . rand(100,999),
            'subtotal' => 10000,
            'tax_amount' => 0,
            'total_amount' => 10000,
            'status' => 'paid', // Simulate FULL flow
            'due_date' => now()->addDays(30),
            'date' => now(),
            'created_by' => $user->id
        ]);

        // NOTE: In a real flow, the Controller handles the GL posting. 
        // Here we ideally should replicate that or call the service if extracted.
        // For simplicity and to show data, we will assume the User goes and clicks "Approve".
        // But to make the "Reports" show something immediately, let's manually inject a Journal Entry if possible,
        // OR rely on the user to click "Approve" in the UI. 
        // Let's rely on the User to "Approve" to see the magic happen live. 
        // I will revert status to 'draft' so they can click Approve.
        
        $invoice->update(['status' => 'draft']);

        // Create/Find Journals (Restored)
        $salesJournal = \App\Models\Accounting\AccountingJournal::firstOrCreate(
            ['code' => 'INV'],
            ['name' => 'Customer Invoices', 'type' => 'sale']
        );

        $purchaseJournal = \App\Models\Accounting\AccountingJournal::firstOrCreate(
            ['code' => 'BILL'],
            ['name' => 'Vendor Bills', 'type' => 'purchase']
        );

        // Fetch Accounts for Journal Items
        $arAccount = \App\Models\Accounting\ChartOfAccount::where('type', 'receivable')->first();
        $incomeAccount = \App\Models\Accounting\ChartOfAccount::where('type', 'income')->first();
        $expenseAccount = \App\Models\Accounting\ChartOfAccount::where('type', 'expense')->first();
        $apAccount = \App\Models\Accounting\ChartOfAccount::where('type', 'payable')->first();

        // Fallbacks if not seeded
        if (!$arAccount) $arAccount = \App\Models\Accounting\ChartOfAccount::create(['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'receivable']);
        if (!$incomeAccount) $incomeAccount = \App\Models\Accounting\ChartOfAccount::create(['code' => '4000', 'name' => 'Sales Info', 'type' => 'income']);
        if (!$expenseAccount) $expenseAccount = \App\Models\Accounting\ChartOfAccount::create(['code' => '6000', 'name' => 'General Expenses', 'type' => 'expense']);
        if (!$apAccount) $apAccount = \App\Models\Accounting\ChartOfAccount::create(['code' => '2100', 'name' => 'Accounts Payable', 'type' => 'payable']);

        $this->command->info('Generating Bulk Demo Data (10+ Records)...');

        $faker = \Faker\Factory::create();
        
        // ... (rest of code)

        // 4. Bulk Customers
        // ... (Keep existing Customer Loop but check if I need to update it? No, previous step fixed the loop structure)
        // Wait, I need to match the previous Replacements logic for the LOOP structure. 
        // I will replace from "Bulk Customers" down to end of loop to ensure clarity.

        $customers = [];
        for ($i = 0; $i < 5; $i++) {
            $customers[] = Customer::firstOrCreate(
                ['company_name' => $faker->company . ' ' . $faker->suffix],
                [
                    // Force ID to bypass model issue
                    'id' => \Illuminate\Support\Str::uuid(),
                    'email' => $faker->unique()->companyEmail,
                    'phone' => $faker->phoneNumber,
                    'vat_number' => $faker->numerify('#########'),
                    'address' => $faker->address,
                    'type' => 'Manager',
                    'status' => 'active',
                    'created_by' => $user->id
                ]
            );
        }

        // 5. Bulk Invoices (Sales)
        foreach ($customers as $index => $customer) {
            for ($j = 0; $j < 3; $j++) {
                $status = $faker->randomElement(['draft', 'sent', 'paid', 'overdue']);
                $date = $faker->dateTimeBetween('-3 months', 'now');
                $dueDate = (clone $date)->modify('+30 days');
                $amount = $faker->randomFloat(2, 1000, 50000);

                $job = ServiceJob::create([
                    'customer_id' => $customer->id,
                    'title' => 'Service Job #' . $faker->randomNumber(4),
                    'job_number' => 'JOB-' . $faker->year . '-' . $faker->randomNumber(5),
                    'status' => 'completed',
                    'created_at' => $date,
                    'created_by' => $user->id
                ]);

                $invoice = Invoice::create([
                    'service_job_id' => $job->id,
                    'invoice_number' => 'INV-' . $faker->year . '-' . $faker->numerify('#####'),
                    'customer_id' => $customer->id,
                    'subtotal' => $amount,
                    'tax_amount' => $amount * 0.05,
                    'total_amount' => $amount * 1.05,
                    'status' => $status == 'overdue' ? 'sent' : $status,
                    'date' => $date,
                    'due_date' => $dueDate,
                    'created_by' => $user->id
                ]);

                // Create Ledger Entry if Paid
                if ($status == 'paid') {
                    $entry = JournalEntry::create([
                        'journal_id' => $salesJournal->id,
                        'name' => $invoice->invoice_number, // Unique Name
                        'reference' => 'Payment for ' . $invoice->invoice_number,
                        'date' => $date,
                        'state' => 'posted',
                        'entity_id' => $invoice->id,
                        'entity_type' => Invoice::class
                    ]);

                    // Debit AR
                    \App\Models\Accounting\JournalItem::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $arAccount->id,
                        'partner_id' => $customer->created_by, 
                        'name' => 'Invoice ' . $invoice->invoice_number,
                        'debit' => $invoice->total_amount,
                        'credit' => 0,
                        'date' => $date
                    ]);

                    // Credit Income
                    \App\Models\Accounting\JournalItem::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $incomeAccount->id,
                        'name' => 'Sales Revenue',
                        'debit' => 0,
                        'credit' => $invoice->total_amount,
                        'date' => $date
                    ]);
                }
            }
        }

        // 6. Bulk Expenses / Bills
        $expenseAccounts = ['Rent', 'Utilities', 'Office Supplies', 'Maintenance'];
        
        for ($k = 0; $k < 10; $k++) {
            $date = $faker->dateTimeBetween('-3 months', 'now');
            $amount = $faker->randomFloat(2, 100, 5000);
            $desc = 'Expense: ' . $faker->randomElement($expenseAccounts);
            
            $entry = JournalEntry::create([
                'journal_id' => $purchaseJournal->id,
                'name' => 'BILL-' . $faker->unique()->numerify('#####'),
                'reference' => 'Vendor Ref ' . $faker->numerify('###'),
                'date' => $date,
                'state' => 'posted',
            ]);

            // Debit Expense
            \App\Models\Accounting\JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $expenseAccount->id,
                'name' => $desc,
                'debit' => $amount,
                'credit' => 0,
                'date' => $date
            ]);

             // Credit AP
             \App\Models\Accounting\JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $apAccount->id,
                'name' => 'Payable',
                'debit' => 0,
                'credit' => $amount,
                'date' => $date
            ]);
        }
        
        $this->command->info("Bulk Data Generation Complete.");
    }
}
