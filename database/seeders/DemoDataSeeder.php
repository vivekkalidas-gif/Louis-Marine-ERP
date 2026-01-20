<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added Log
use App\Models\User;
use App\Models\InventoryItem;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Accounting\ChartOfAccount;
use App\Models\Accounting\CostCenter;
use App\Models\Accounting\Project;
use App\Models\Accounting\Budget;
use App\Models\Assets\FixedAsset; 
use App\Models\Accounting\LandedCost;
use App\Models\Invoice; 
use App\Models\VendorBill; 
use App\Models\Payment; 
use App\Models\VendorPayment; 
use App\Models\Expense; 
use App\Models\EmployeeDetail; 
use App\Models\FinanceCategory;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        if (!$user) {
            echo "No user found, skipping demo data linked to user.\n";
            return;
        }
        $adminId = $user->id;

        // 1. Suppliers
        try {
            echo "Seeding Suppliers...\n";
            $sup1 = Supplier::firstOrCreate(['email' => 'supply@globalmarine.com'], [
                'name' => 'Global Marine Supplies',
                'phone' => '12345678',
                'address' => 'Doha Port',
                'contact_person' => 'John Doe'
            ]);
            $sup2 = Supplier::firstOrCreate(['email' => 'logistics@fastship.com'], [
                'name' => 'FastShip Logistics',
                'phone' => '87654321',
                'address' => 'Industrial Area',
                'contact_person' => 'Jane Smith'
            ]);
        } catch (\Exception $e) {
            echo "Error seeding Suppliers: " . $e->getMessage() . "\n";
            return; // Stop if core data fails
        }

        // 2. Cost Centers
        try {
            echo "Seeding Cost Centers...\n";
            $cc1 = CostCenter::firstOrCreate(['code' => 'CC-OPS'], ['name' => 'Operations', 'type' => 'department']);
            $cc2 = CostCenter::firstOrCreate(['code' => 'CC-ADM'], ['name' => 'Administration', 'type' => 'department']);
        } catch (\Exception $e) { echo "Error seeding Cost Centers: " . $e->getMessage() . "\n"; }

        // 3. Projects
        try {
             echo "Seeding Projects...\n";
             $proj1 = Project::firstOrCreate(['code' => 'PRJ-2026-001'], ['name' => 'Vessel Refit - Titanic II', 'status' => 'active', 'start_date' => now()]);
             $proj2 = Project::firstOrCreate(['code' => 'PRJ-2026-002'], ['name' => 'Annual Maintenance - Queen Mary', 'status' => 'planning', 'start_date' => now()->addMonth()]);
        } catch (\Exception $e) { echo "Error seeding Projects: " . $e->getMessage() . "\n"; }

        // 4. Budgets
        try {
            echo "Seeding Budgets...\n";
            Budget::firstOrCreate(['fiscal_year' => 2026, 'name' => 'Operations Budget 2026'], [
                'total_amount' => 500000,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31'
            ]);
            Budget::firstOrCreate(['fiscal_year' => 2026, 'name' => 'Admin Budget 2026'], [
                'total_amount' => 200000,
                'start_date' => '2026-01-01',
                'end_date' => '2026-12-31'
            ]);
        } catch (\Exception $e) { echo "Error seeding Budgets: " . $e->getMessage() . "\n"; }

        // 5. Fixed Assets
        try {
            echo "Seeding Fixed Assets...\n";
            $assetAcc = ChartOfAccount::where('type', 'fixed_asset')->first();
            if ($assetAcc) {
                FixedAsset::firstOrCreate(['asset_code' => 'FA-001'], [
                    'asset_name' => 'Delivery Truck',
                    'purchase_date' => now()->subYear(),
                    'purchase_cost' => 120000,
                    'useful_life_years' => 5,
                    'salvage_value' => 20000,
                    'status' => 'active',
                    'asset_account_id' => $assetAcc->id
                ]);
                FixedAsset::firstOrCreate(['asset_code' => 'FA-002'], [
                    'asset_name' => 'Office Laptop',
                    'purchase_date' => now()->subMonths(3),
                    'purchase_cost' => 5000,
                    'useful_life_years' => 3,
                    'salvage_value' => 500,
                    'status' => 'active',
                    'asset_account_id' => $assetAcc->id
                ]);
            }
        } catch (\Exception $e) { echo "Error seeding Fixed Assets: " . $e->getMessage() . "\n"; }

        // 6. Purchase Orders
        $po1 = null;
        try {
            echo "Seeding POs...\n";
            $item1 = InventoryItem::first() ?? InventoryItem::factory()->create();
            $item2 = InventoryItem::skip(1)->first() ?? InventoryItem::factory()->create();

            if (PurchaseOrder::count() < 2) {
                $po1 = PurchaseOrder::create([
                    'po_number' => 'PO-' . rand(1000, 9999),
                    'supplier_id' => $sup1->id,
                    'po_date' => now()->subDays(5),
                    'status' => 'received',
                    'total_amount' => 5000,
                    'created_by' => $adminId
                ]);
                $po1->items()->create(['inventory_item_id' => $item1->id, 'quantity' => 10, 'unit_price' => 200, 'total_price' => 2000]);
                $po1->items()->create(['inventory_item_id' => $item2->id, 'quantity' => 20, 'unit_price' => 150, 'total_price' => 3000]);

                $po2 = PurchaseOrder::create([
                    'po_number' => 'PO-' . rand(1000, 9999),
                    'supplier_id' => $sup2->id,
                    'po_date' => now(),
                    'status' => 'pending',
                    'total_amount' => 2000,
                    'created_by' => $adminId
                ]);
                $po2->items()->create(['inventory_item_id' => $item1->id, 'quantity' => 5, 'unit_price' => 200, 'total_price' => 1000]);
            } else {
                 $po1 = PurchaseOrder::first();
            }
        } catch (\Exception $e) { echo "Error seeding POs: " . $e->getMessage() . "\n"; }

        // 7. Landed Costs
        try {
             if (LandedCost::count() < 1 && $po1) {
                echo "Seeding Landed Costs...\n";
                $lc = LandedCost::create([
                    'purchase_order_id' => $po1->id,
                    'date' => now(),
                    'reference' => 'FRT-DEMO-01',
                    'status' => 'posted',
                    'total_amount' => 500,
                    'created_by' => $adminId
                ]);
                $freightAcc = ChartOfAccount::firstOrCreate(['code' => 'EXP-FRT'], ['name' => 'Freight Expense', 'type' => 'expense']);
                $lc->lines()->create([
                    'account_id' => $freightAcc->id,
                    'description' => 'Ocean Freight',
                    'amount' => 500,
                    'allocation_method' => 'value'
                ]);
             }
        } catch (\Exception $e) { echo "Error seeding Landed Costs: " . $e->getMessage() . "\n"; }

        // 8. Invoices (Sales)
        try {
             echo "Seeding Invoices...\n";
            $customer = \App\Models\Customer::first(); 
            if ($customer && Invoice::count() < 2) {
                $invNum1 = 'INV-' . rand(10000, 99999);
                $invNum2 = 'INV-' . rand(10000, 99999);
                
                $inv1 = Invoice::create([
                    'invoice_number' => $invNum1,
                    'customer_id' => $customer->id,
                    'date' => now()->subDays(2),
                    'due_date' => now()->addDays(28),
                    'status' => 'sent',
                    'total_amount' => 1500
                ]);
                
                $inv2 = Invoice::create([
                    'invoice_number' => $invNum2,
                    'customer_id' => $customer->id,
                    'date' => now(),
                    'due_date' => now()->addDays(30),
                    'status' => 'draft',
                    'total_amount' => 2500
                ]);
            }
        } catch (\Exception $e) { echo "Error seeding Invoices: " . $e->getMessage() . "\n"; }

        // 9. Vendor Bills (AP)
        try {
             echo "Seeding Vendor Bills...\n";
            if (VendorBill::count() < 2) {
                $bill1 = VendorBill::create([
                    'bill_number' => 'BILL-' . rand(1000, 9999),
                    'supplier_id' => $sup1->id,
                    'bill_date' => now()->subDays(5),
                    'due_date' => now()->addDays(25),
                    'status' => 'posted',
                    'total_amount' => 5000,
                    'purchase_order_id' => isset($po1) ? $po1->id : null
                ]);
                
                $bill2 = VendorBill::create([
                    'bill_number' => 'BILL-' . rand(1000, 9999),
                    'supplier_id' => $sup2->id,
                    'bill_date' => now(),
                    'due_date' => now()->addDays(30),
                    'status' => 'draft',
                    'total_amount' => 2000
                ]);
            }
        } catch (\Exception $e) { echo "Error seeding Vendor Bills: " . $e->getMessage() . "\n"; }

        // 10. Payments
        try {
             echo "Seeding Payments...\n";
            if (VendorPayment::count() < 1) {
                 VendorPayment::create([
                     'payment_number' => 'PAY-V-' . rand(1000, 9999),
                     'vendor_id' => $sup1->id,
                     'amount' => 2500,
                     'payment_date' => now(),
                     'payment_method' => 'bank_transfer',
                     'status' => 'posted'
                ]);
            }
            
            if (Payment::count() < 1) {
                 Payment::create([
                     'receipt_number' => 'RCPT-' . rand(1000, 9999),
                     'customer_id' => $customer ? $customer->id : null,
                     'amount' => 500,
                     'payment_date' => now(),
                     'payment_method' => 'cash',
                     'status' => 'posted'
                ]);
            }
        } catch (\Exception $e) { echo "Error seeding Payments: " . $e->getMessage() . "\n"; }

        // 11. Expenses
        try {
             echo "Seeding Expenses...\n";
            $cat = FinanceCategory::firstOrCreate(['name' => 'Office Supplies', 'type' => 'expense']);
            if (Expense::count() < 2) {
                Expense::create([
                    'reference_no' => 'EXP-' . rand(1000, 9999),
                    'category_id' => $cat->id,
                    'amount' => 150,
                    'expense_date' => now()->subDay(),
                    'description' => 'Printer Paper',
                    'status' => 'approved'
                ]);
                Expense::create([
                    'reference_no' => 'EXP-' . rand(1000, 9999),
                    'category_id' => $cat->id,
                    'amount' => 50,
                    'expense_date' => now(),
                    'description' => 'Coffee & Tea',
                    'status' => 'pending'
                ]);
            }
        } catch (\Exception $e) { echo "Error seeding Expenses: " . $e->getMessage() . "\n"; }

        // 12. Payroll
        try {
             echo "Seeding Payroll...\n";
            $emp1 = User::skip(1)->first(); 
            if ($emp1) {
                EmployeeDetail::firstOrCreate(['user_id' => $emp1->id], [
                    'employee_code' => 'EMP-001-' . $emp1->id,
                    'department' => 'Operations',
                    'designation' => 'Technician',
                    'basic_salary' => 5000,
                    'join_date' => '2025-01-01'
                ]);
            }
        } catch (\Exception $e) { echo "Error seeding Payroll: " . $e->getMessage() . "\n"; }
        
        echo "Demo Data Seeding Completed.\n";
    }
}
