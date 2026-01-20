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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained()->onDelete('cascade');
            $table->date('date');
            
            // invoices, payments, adjustments, etc.
            $table->string('type'); 
            
            // Polymorphic relation to Invoice or Payment
            $table->uuid('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            
            $table->string('description')->nullable();
            
            $table->decimal('debit', 15, 2)->default(0);  // Increases balance (e.g., Invoice)
            $table->decimal('credit', 15, 2)->default(0); // Decreases balance (e.g., Payment)
            $table->decimal('running_balance', 15, 2)->default(0); // Snapshot
            
            $table->timestamps();
            
            // Optimizations
            $table->index(['customer_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
