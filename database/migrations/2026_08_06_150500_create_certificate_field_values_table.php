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
        if (!Schema::hasTable('certificate_field_values')) {
            Schema::create('certificate_field_values', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('certificate_id')->constrained('certificates')->cascadeOnDelete();
                $table->foreignUuid('certificate_template_field_id')->constrained('certificate_template_fields')->cascadeOnDelete();
                $table->integer('section_instance')->default(0);
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_field_values');
    }
};
