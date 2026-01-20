<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chart of Accounts
        if (!Schema::hasTable('chart_of_accounts')) {
            Schema::create('chart_of_accounts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('code')->index();
                $table->string('name');
                $table->string('type'); // asset, liability, equity, income, expense
                $table->boolean('is_reconcilable')->default(false);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Accounting Journals
        if (!Schema::hasTable('accounting_journals')) {
            Schema::create('accounting_journals', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('code')->index();
                $table->string('type'); // sale, purchase, cash, bank, general
                $table->foreignId('currency_id')->nullable(); 
                // Note: using foreignId (BigInt) as per currency table, or checking currency table type?
                // Currencies table creates id() (BigInt). So foreignId is correct.
                
                $table->foreignUuid('default_account_id')->nullable()->constrained('chart_of_accounts');
                $table->uuid('created_by')->nullable(); // Unconstrained to avoid User dependency loop or type issues
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Journal Entries
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('journal_id')->constrained('accounting_journals')->cascadeOnDelete();
                $table->date('date');
                $table->string('reference')->nullable();
                $table->string('state')->default('draft'); // draft, posted, cancelled
                $table->foreignId('currency_id')->nullable(); 
                $table->uuid('partner_id')->nullable(); // Polymorphic or just generic ID (Customer/Supplier)
                $table->uuid('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Journal Items
        if (!Schema::hasTable('journal_items')) {
            Schema::create('journal_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
                $table->foreignUuid('account_id')->constrained('chart_of_accounts');
                $table->string('label')->nullable();
                $table->decimal('debit', 15, 2)->default(0);
                $table->decimal('credit', 15, 2)->default(0);
                $table->date('date'); // Denormalized for query checks
                $table->uuid('partner_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('accounting_journals');
        Schema::dropIfExists('chart_of_accounts');
    }
};
