<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->enum('movement_type', ['inward', 'outward', 'adjustment', 'return']);
            $table->string('reference_type')->nullable(); // Job, Purchase, Adjustment
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('quantity');
            $table->integer('balance_after'); // Stock after this movement
            $table->string('location')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['inventory_item_id', 'created_at']);
            $table->index('movement_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
