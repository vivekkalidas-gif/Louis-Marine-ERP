<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update Suppliers Table
        Schema::table('suppliers', function (Blueprint $table) {
            if (!Schema::hasColumn('suppliers', 'opening_balance')) {
                $table->decimal('opening_balance', 15, 2)->default(0)->after('rating');
            }
            // Payment terms already exist
        });

        // 2. Create Vendor Bills Table
        Schema::create('vendor_bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vendor_id')->constrained('suppliers');
            $table->string('bill_number'); // Vendor's invoice number
            $table->date('bill_date');
            $table->date('due_date');
            $table->string('status')->default('draft'); // draft, posted, paid, partial, void
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0); // Total Payable
            $table->text('notes')->nullable();
            
            // Accounting Link
            $table->foreignUuid('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('posted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint: vendor + bill number
            $table->unique(['vendor_id', 'bill_number']);
        });

        // 3. Create Vendor Bill Items Table
        Schema::create('vendor_bill_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vendor_bill_id')->constrained('vendor_bills')->cascadeOnDelete();
            
            // Link to expense account
            $table->foreignUuid('account_id')->constrained('chart_of_accounts'); 
            
            // Optional: Link to Inventory Item if purchasing stock
            // $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items');
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            
            $table->string('description');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2); // (Qty * Price) + Tax
            
            $table->timestamps();
        });

        // 4. Create Vendor Payments Table
        Schema::create('vendor_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('payment_number')->unique();
            $table->foreignUuid('vendor_id')->constrained('suppliers');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, bank, card, check, transfer
            $table->string('reference')->nullable(); // Check number, Transaction ID
            $table->string('status')->default('draft'); // draft, posted, void
            $table->text('notes')->nullable();
            
            // Accounting Link
            $table->foreignUuid('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
             // Account paid FROM (Bank/Cash)
            $table->foreignUuid('bank_account_id')->nullable()->constrained('chart_of_accounts');
            
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Create Pivot Table for Payment Allocation (Many-to-Many)
        // A single payment can pay multiple bills.
        // A single bill can be paid by multiple payments (partial).
        Schema::create('vendor_bill_payment', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('vendor_payment_id')->constrained('vendor_payments')->cascadeOnDelete();
            $table->foreignUuid('vendor_bill_id')->constrained('vendor_bills')->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // Amount of this payment applied to this bill
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_bill_payment');
        Schema::dropIfExists('vendor_payments');
        Schema::dropIfExists('vendor_bill_items');
        Schema::dropIfExists('vendor_bills');
        
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'opening_balance')) {
                $table->dropColumn('opening_balance');
            }
        });
    }
};
