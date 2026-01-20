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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dr_number')->unique();
            $table->date('date');
            $table->foreignUuid('customer_id')->constrained('customers'); // Fixed: foreignId -> foreignUuid
            $table->string('reference')->nullable();
            $table->string('status')->default('draft');
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignUuid('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id(); // Using BigInt ID for item rows
            $table->foreignUuid('delivery_note_id')->constrained('delivery_notes')->cascadeOnDelete();
            $table->unsignedBigInteger('inventory_item_id')->nullable(); // Matches InventoryItem ID type (BigInt)
            $table->string('description');
            $table->decimal('quantity', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_note_items');
        Schema::dropIfExists('delivery_notes');
    }
};
