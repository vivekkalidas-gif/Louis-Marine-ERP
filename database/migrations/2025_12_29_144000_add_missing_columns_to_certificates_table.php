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
        Schema::table('certificates', function (Blueprint $table) {
            // Template functionality removed for now
            // if (!Schema::hasColumn('certificates', 'certificate_template_id')) {
            //    $table->foreignUuid('certificate_template_id')->nullable()->constrained('certificate_templates')->nullOnDelete();
            // }
            if (!Schema::hasColumn('certificates', 'equipment_category_id')) {
                $table->foreignId('equipment_category_id')->nullable()->constrained('equipment_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('certificates', 'job_number')) {
                $table->string('job_number')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'remarks')) {
                $table->text('remarks')->nullable();
            }
            if (!Schema::hasColumn('certificates', 'issued_by')) {
                $table->foreignUuid('issued_by')->nullable()->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Drop logic if needed, but safe to skip for add-only helper
        });
    }
};
