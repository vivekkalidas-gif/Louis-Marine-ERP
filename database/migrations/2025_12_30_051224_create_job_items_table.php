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
        Schema::create('job_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_job_id')->constrained('service_jobs')->onDelete('cascade');
            $table->string('name'); // Item name (e.g. 5kg CO2 Extinguisher)
            $table->integer('quantity')->default(1);
            $table->string('description')->nullable(); // Serial numbers or notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_items');
    }
};
