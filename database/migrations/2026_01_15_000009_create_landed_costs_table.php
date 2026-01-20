<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('landed_costs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete(); // BigInt from PO table
            $table->string('reference')->nullable();
            $table->date('date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('landed_cost_lines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('landed_cost_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_id')->constrained('chart_of_accounts'); // Expense/Clearing
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->enum('allocation_method', ['value', 'quantity', 'manual'])->default('value');
            $table->timestamps();
        });
        
        // Optional: Stores the exact amount allocated to each item for audit
        Schema::create('landed_cost_allocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('landed_cost_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2); // The allocated cost
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('landed_cost_allocations');
        Schema::dropIfExists('landed_cost_lines');
        Schema::dropIfExists('landed_costs');
    }
};
