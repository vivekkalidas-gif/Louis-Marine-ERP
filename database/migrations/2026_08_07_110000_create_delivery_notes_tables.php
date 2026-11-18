<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('delivery_notes')) {
            Schema::create('delivery_notes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('dr_number')->nullable();
                $table->date('date')->nullable();
                $table->uuid('customer_id')->nullable();
                $table->string('reference')->nullable();
                $table->string('status')->default('draft');
                $table->uuid('invoice_id')->nullable();
                $table->uuid('quotation_id')->nullable();
                $table->text('notes')->nullable();
                $table->uuid('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('delivery_note_items')) {
            Schema::create('delivery_note_items', function (Blueprint $table) {
                $table->id();
                $table->uuid('delivery_note_id');
                $table->uuid('inventory_item_id')->nullable();
                $table->text('description')->nullable();
                $table->decimal('quantity', 15, 2)->default(1);
                $table->timestamps();

                $table->foreign('delivery_note_id')->references('id')->on('delivery_notes')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_note_items');
        Schema::dropIfExists('delivery_notes');
    }
};
