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
        if (!Schema::hasTable('inventory_transactions')) {
            Schema::create('inventory_transactions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
                $table->string('type'); // IN, OUT, ADJUSTMENT
                $table->integer('quantity'); 
                $table->string('reference')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
