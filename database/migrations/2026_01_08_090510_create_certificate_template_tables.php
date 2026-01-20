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
        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->nullOnDelete();
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->json('header_config')->nullable(); // {show_logo: true, show_company: true}
            $table->json('footer_config')->nullable(); // {show_qr: true, show_signature: true}
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certificate_template_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('certificate_template_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Section Title (e.g., "Equipment Details")
            $table->string('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_repeatable')->default(false); // For things like "Cylinder List"
            $table->string('layout_type')->default('two_column'); // 'single_column', 'two_column', 'table'
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certificate_template_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('section_id')->constrained('certificate_template_sections')->cascadeOnDelete();
            $table->string('field_name'); // internal key (e.g., 'serial_no')
            $table->string('field_label'); // Display Label (e.g., "Serial Number")
            $table->string('field_type'); // text, number, date, dropdown, checkbox, textarea
            $table->json('field_options')->nullable(); // For dropdowns: {options: ['A', 'B']}
            $table->boolean('is_required')->default(false);
            $table->string('validation_rules')->nullable();
            $table->string('default_value')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->integer('display_order')->default(0);
            $table->string('width')->default('full'); // full, half, third
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_template_fields');
        Schema::dropIfExists('certificate_template_sections');
        Schema::dropIfExists('certificate_templates');
    }
};
