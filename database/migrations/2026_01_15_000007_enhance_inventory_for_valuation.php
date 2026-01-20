<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Enhance Inventory Items for Costing
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('costing_method')->default('weighted_average')->after('status'); // weighted_average, fifo, standard
            $table->decimal('std_cost', 15, 2)->default(0)->after('costing_method');
            
            // GL Accounts
            $table->foreignUuid('inventory_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignUuid('cogs_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->foreignUuid('sales_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
        });

        // 2. Fix and Enhance Inventory Transactions
        // We need to fix the product_id type mismatch. 
        // Strategy: Drop the column and recreate it correctly as inventory_item_id (BigInt)
        // Also add valuation columns.
        
        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Drop old Foreign Key if exists (uuid one)
            // Note: DBAL might struggle with dropping foreign keys by column name sometimes, but we try standard way.
            // We'll assumme strict mode off or handle manually if it fails.
            
            // If product_id exists, we drop it. ideally we migrate data, but assuming dev env for now.
            // But to be safe, we just add `inventory_item_id` and deprecate `product_id`.
            
            $table->foreignId('inventory_item_id')->nullable()->after('id')->constrained('inventory_items')->cascadeOnDelete();
            
            // Valuation Columns
            $table->decimal('unit_cost', 15, 4)->default(0)->after('quantity');
            $table->decimal('total_cost', 15, 2)->default(0)->after('unit_cost');
            $table->decimal('stock_value_after', 15, 2)->default(0)->after('total_cost');
            
            $table->string('batch_no')->nullable()->after('reference');
            
            // Link to Journal Entry
            $table->foreignUuid('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_account_id']);
            $table->dropForeign(['cogs_account_id']);
            $table->dropForeign(['sales_account_id']);
            $table->dropColumn(['costing_method', 'std_cost', 'inventory_account_id', 'cogs_account_id', 'sales_account_id']);
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropForeign(['journal_entry_id']);
            $table->dropColumn(['inventory_item_id', 'unit_cost', 'total_cost', 'stock_value_after', 'batch_no', 'journal_entry_id']);
        });
    }
};
