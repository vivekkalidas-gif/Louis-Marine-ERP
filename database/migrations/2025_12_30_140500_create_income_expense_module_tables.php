<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Finance Categories
        Schema::create('finance_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('type'); // 'income', 'expense'
            $table->string('color')->default('gray'); // for UI badges
            $table->boolean('is_system')->default(false); // cannot be deleted
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Incomes
        Schema::create('incomes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            
            $table->foreignUuid('category_id')->constrained('finance_categories');
            $table->foreignUuid('customer_id')->nullable()->constrained()->nullOnDelete();
            
            $table->decimal('amount', 15, 2);
            $table->string('payment_method'); // cash, bank_transfer, cheque
            
            // Reference to other records (e.g., Payment, Invoice)
            $table->uuid('reference_id')->nullable();
            $table->string('reference_type')->nullable(); // App\Models\Payment, App\Models\Invoice
            
            $table->string('reference_number')->nullable(); // Manual ref number
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            
            $table->string('status')->default('approved'); // approved, pending
            $table->foreignUuid('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            
            $table->foreignUuid('category_id')->constrained('finance_categories');
            $table->foreignUuid('customer_id')->nullable()->constrained()->nullOnDelete();
            
            // Link to Service Jobs for Cost Analysis
            $table->foreignUuid('job_id')->nullable()->constrained('service_jobs')->nullOnDelete();
            
            $table->string('vendor_name')->nullable();
            $table->string('reference_number')->nullable(); // Bill/Invoice #
            
            $table->decimal('amount', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            
            $table->string('payment_method');
            $table->text('notes')->nullable();
            
            $table->foreignUuid('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('finance_categories');
    }
};
