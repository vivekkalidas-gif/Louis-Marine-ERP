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
        Schema::create('certificate_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('validity_months')->default(12);
            $table->text('description')->nullable();
            $table->text('template_structure')->nullable(); // JSON or HTML structure
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_types');
    }
};
