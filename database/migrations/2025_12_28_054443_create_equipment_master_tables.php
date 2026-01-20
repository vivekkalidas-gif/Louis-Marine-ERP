<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Equipment Categories (replaces scope_categories)
        Schema::create('equipment_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable(); // For UI display
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Equipment Items (replaces scope_items with dynamic attributes)
        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('equipment_categories')->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('code', 50)->nullable()->unique(); // e.g., "FE-CO2-5KG"
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->text('description')->nullable();
            $table->json('attributes')->nullable(); // Dynamic attributes per equipment type
            $table->string('unit', 20)->default('Nos'); // Nos, Sets, Ltrs, Pairs
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('category_id');
            $table->index('is_active');
        });

        // 3. Equipment Attribute Templates (optional - for UI hints)
        Schema::create('equipment_attribute_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('equipment_categories')->cascadeOnDelete();
            $table->string('attribute_key', 100); // e.g., "capacity_kg"
            $table->string('attribute_label', 100); // e.g., "Capacity (KG)"
            $table->enum('attribute_type', ['text', 'number', 'select', 'boolean', 'date'])->default('text');
            $table->json('options')->nullable(); // For select type: ["CO2", "DCP", "Foam"]
            $table->boolean('is_required')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('category_id');
        });

        // 4. Job Equipment (replaces job_scopes)
        Schema::create('job_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignId('equipment_item_id')->constrained('equipment_items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->json('serial_numbers')->nullable(); // Array of serial numbers for tracked items
            $table->text('condition_notes')->nullable(); // Notes about equipment condition
            $table->json('technician_status')->nullable(); // {"serviced": true, "replaced": false, "refilled": true}
            $table->timestamps();
            
            $table->index('service_job_id');
            $table->index('equipment_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_equipment');
        Schema::dropIfExists('equipment_attribute_templates');
        Schema::dropIfExists('equipment_items');
        Schema::dropIfExists('equipment_categories');
    }
};
