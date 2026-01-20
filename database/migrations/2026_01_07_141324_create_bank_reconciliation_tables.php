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
        // 1. Bank Statements
        // 1. Bank Statements
        if (!Schema::hasTable('bank_statements')) {
            Schema::create('bank_statements', function (Blueprint $table) {
                $table->uuid('id')->primary();
                
                $table->string('name'); // Statement Name e.g., "BNK1/2024/01"
                $table->date('date'); // Statement/Closing Date
                
                $table->foreignUuid('journal_id')->constrained('accounting_journals'); // Which Bank Journal
                
                $table->decimal('balance_start', 15, 2);
                $table->decimal('balance_end_real', 15, 2); // Ending Balance on Bank Statement
                
                // Computed ending balance based on lines
                // $table->decimal('balance_end_formatted', 15, 2); 

                $table->string('state')->default('open'); // open, posted, confirm (validated)
                
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Bank Statement Lines (The transactions on the bank statement)
        if (!Schema::hasTable('bank_statement_lines')) {
            Schema::create('bank_statement_lines', function (Blueprint $table) {
                $table->uuid('id')->primary();
                
                $table->foreignUuid('statement_id')->constrained('bank_statements')->cascadeOnDelete();
                
                $table->date('date');
                $table->string('label'); // Description from bank
                $table->string('partner_name')->nullable(); // Raw partner name from bank
                $table->string('ref')->nullable(); // Transaction Ref
                
                $table->decimal('amount', 15, 2); // Negative for expense, Positive for income
                $table->decimal('amount_currency', 15, 2)->nullable(); // For multi-currency
                
                // Link to Journal Entry if Reconciled
                $table->foreignUuid('journal_entry_id')->nullable()->constrained('journal_entries');
                // Or link specific item? Usually match against Journal Item (Liquidity Line)
                // But Odoo matches against Journal Item. 
                // For simplicity, let's say if we match an existing payment, we link to its Entry.
                
                $table->string('status')->default('pending'); // pending, reconciled
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
        Schema::dropIfExists('bank_statements');
    }
};
