<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Vessel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Accounts Role
        $role = Role::firstOrCreate(
            ['slug' => 'accounts'],
            ['name' => 'Accounts Officer']
        );

        // 2. Create Demo User
        $user = User::firstOrCreate(
            ['email' => 'accounts@louismarine.qa'],
            [
                'name' => 'Accounts Officer',
                'password' => Hash::make('password'),
            ]
        );
        $user->roles()->syncWithoutDetaching([$role->id]);

        // 3. Demo Invoices
        // Ensure we have a customer
        $customer = Customer::first();
        if (!$customer) {
            $customer = Customer::create([
                'company_name' => 'Demo Customer',

                'email' => 'demo@example.com',
                'phone' => '12345678',
                'address' => 'Doha, Qatar'
            ]);
        }

        // Invoice 1: Paid
        $invoice1 = Invoice::create([
            'invoice_number' => 'INV-2025-001',
            'customer_id' => $customer->id,
            'date' => now()->subDays(10),
            'due_date' => now()->addDays(20),
            'status' => 'paid',
            'subtotal' => 1000,
            'tax_amount' => 50, // 5% VAT
            'total_amount' => 1050,
            'paid_amount' => 1050,
            'notes' => 'Full payment received.',
            'created_by' => $user->id
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice1->id,
            'description' => 'Marine Equipment Service',
            'quantity' => 1,
            'unit_price' => 1000,
            'amount' => 1000
        ]);
        Payment::create([
            'payment_number' => 'PAY-001',
            'invoice_id' => $invoice1->id,
            'payment_date' => now()->subDays(5),
            'amount' => 1050,
            'payment_method' => 'bank_transfer',
            'reference_number' => 'TRX123456',
            'created_by' => $user->id
        ]);

        // Invoice 2: Partial
        $invoice2 = Invoice::create([
            'invoice_number' => 'INV-2025-002',
            'customer_id' => $customer->id,
            'date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'status' => 'partial',
            'subtotal' => 2000,
            'tax_amount' => 100,
            'total_amount' => 2100,
            'paid_amount' => 1000,
            'notes' => 'Partial payment made.',
            'created_by' => $user->id
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice2->id,
            'description' => 'Spare Parts Supply',
            'quantity' => 2,
            'unit_price' => 1000,
            'amount' => 2000
        ]);
        Payment::create([
            'payment_number' => 'PAY-002',
            'invoice_id' => $invoice2->id,
            'payment_date' => now()->subDays(1),
            'amount' => 1000,
            'payment_method' => 'cash',
            'created_by' => $user->id
        ]);

        // Invoice 3: Unpaid / Overdue
        $invoice3 = Invoice::create([
            'invoice_number' => 'INV-2025-003',
            'customer_id' => $customer->id,
            'date' => now()->subDays(35),
            'due_date' => now()->subDays(5),
            'status' => 'overdue',
            'subtotal' => 500,
            'tax_amount' => 25,
            'total_amount' => 525,
            'paid_amount' => 0,
            'notes' => 'Payment overdue.',
            'created_by' => $user->id
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice3->id,
            'description' => 'Emergency Repair',
            'quantity' => 1,
            'unit_price' => 500,
            'amount' => 500
        ]);
    }
}
