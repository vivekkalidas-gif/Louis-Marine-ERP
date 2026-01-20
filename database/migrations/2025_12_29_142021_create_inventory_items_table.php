<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('category'); // Fire Extinguisher, CO₂ System, SCBA, etc.
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->enum('unit', ['Nos', 'Kg', 'Set', 'Ltr', 'Mtr'])->default('Nos');
            $table->integer('minimum_stock_level')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->integer('current_stock')->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index('category');
            $table->index('status');
            $table->index('current_stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
