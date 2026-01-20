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
        // 1. Drop the incorrect UUID column (and its constraint)
        Schema::table('quotation_items', function (Blueprint $table) {
            // Drop foreign key if it exists (using array syntax to guess name)
            // Name likely: quotation_items_inventory_item_id_foreign
            $table->dropForeign(['inventory_item_id']); 
            $table->dropColumn('inventory_item_id');
        });

        // 2. Add the correct BigInt column
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn('inventory_item_id');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignUuid('inventory_item_id')->nullable()->constrained('inventory_items');
        });
    }
};
