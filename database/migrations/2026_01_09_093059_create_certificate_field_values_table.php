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
        Schema::create('certificate_field_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignUuid('certificate_id')
                  ->constrained('certificates')
                  ->cascadeOnDelete();
                  
            // We reference the field definition
            $table->foreignUuid('certificate_template_field_id')
                  ->constrained('certificate_template_fields')
                  ->cascadeOnDelete();
            
            // The actual value stored. 
            // We use 'longText' to accommodate potentially large data or JSON
            $table->longText('value')->nullable();
            
            $table->timestamps();

            // Index for faster lookups
            $table->index(['certificate_id', 'certificate_template_field_id'], 'cert_field_val_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_field_values');
    }
};
