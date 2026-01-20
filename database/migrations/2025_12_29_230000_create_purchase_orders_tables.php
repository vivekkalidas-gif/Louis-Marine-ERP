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
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignUuid('supplier_id')->constrained()->cascadeOnDelete();
                $table->string('po_number')->unique();
                $table->date('po_date');
                $table->date('expected_delivery_date')->nullable();
                $table->string('status')->default('draft'); // draft, sent, partial, received, cancelled
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
                $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity'); // Or decimal if needed
                $table->decimal('unit_price', 15, 2);
                $table->decimal('total_price', 15, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
