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
        Schema::create('certificate_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('certificate_id')->constrained()->cascadeOnDelete();
            
            $table->string('equipment_type')->nullable(); // e.g. "Dry Powder 6kg"
            $table->string('serial_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->date('manufacture_date')->nullable();
            
            $table->date('last_service_date')->nullable();
            $table->date('next_due_date')->nullable();
            
            $table->string('condition')->nullable(); // "good", "repair", etc.
            $table->json('details')->nullable(); // Extra fields like Location, Capacity
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_items');
    }
};
