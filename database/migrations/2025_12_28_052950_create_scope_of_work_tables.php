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
        // 1. Categories (e.g., Fire Extinguishers, SCBA, Lifeboats)
        Schema::create('scope_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 2. Items (e.g., CO2 5kg, SCBA 30min)
        Schema::create('scope_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('scope_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->default('Nos'); // Nos, Sets, Ltrs
            $table->json('default_checklist')->nullable(); // Predefined checks
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Job Scopes (Pivot + Data)
        Schema::create('job_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignId('scope_item_id')->constrained('scope_items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->string('remarks')->nullable(); // Specific location or note
            $table->json('technician_status')->nullable(); // { "serviced": true, "refilled": false }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_scopes');
        Schema::dropIfExists('scope_items');
        Schema::dropIfExists('scope_categories');
    }
};
