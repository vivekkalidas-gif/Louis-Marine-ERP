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
        if (!Schema::hasTable('certificate_templates')) {
            Schema::create('certificate_templates', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->nullOnDelete();
                $table->string('title')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->json('header_config')->nullable();
                $table->json('footer_config')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('certificate_template_sections')) {
            Schema::create('certificate_template_sections', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('certificate_template_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('description')->nullable();
                $table->integer('display_order')->default(0);
                $table->boolean('is_repeatable')->default(false);
                $table->string('layout_type')->default('two_column');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('certificate_template_fields')) {
            Schema::create('certificate_template_fields', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('section_id')->constrained('certificate_template_sections')->cascadeOnDelete();
                $table->string('field_name');
                $table->string('field_label');
                $table->string('field_type');
                $table->json('field_options')->nullable();
                $table->boolean('is_required')->default(false);
                $table->string('validation_rules')->nullable();
                $table->string('default_value')->nullable();
                $table->string('placeholder')->nullable();
                $table->string('help_text')->nullable();
                $table->integer('display_order')->default(0);
                $table->string('width')->default('full');
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
        Schema::dropIfExists('certificate_template_fields');
        Schema::dropIfExists('certificate_template_sections');
        Schema::dropIfExists('certificate_templates');
    }
};
