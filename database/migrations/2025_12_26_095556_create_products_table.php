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
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('inventory_category_id')->constrained()->cascadeOnDelete();
                $table->foreignUuid('supplier_id')->nullable()->constrained()->nullOnDelete();
                $table->string('sku')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->integer('current_stock')->default(0);
                $table->integer('min_stock_level')->default(5);
                $table->string('location')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
