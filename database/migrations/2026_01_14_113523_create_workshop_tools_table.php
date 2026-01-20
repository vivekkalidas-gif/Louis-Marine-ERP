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
        Schema::create('workshop_tools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique()->nullable();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            // We can reuse EquipmentCategory or create a new internal category.
            // Removing constraint for now to avoid migration errors if table doesn't match perfectly.
            $table->unsignedBigInteger('category_id')->nullable();
            //$table->foreign('category_id')->references('id')->on('equipment_categories')->nullOnDelete();
            
            $table->string('unit')->default('Nos');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_tools');
    }
};
