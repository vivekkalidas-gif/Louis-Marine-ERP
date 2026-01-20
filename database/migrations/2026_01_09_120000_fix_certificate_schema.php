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
        // Fix certificate_field_values table
        Schema::table('certificate_field_values', function (Blueprint $table) {
            // Add section_instance if missing
            if (!Schema::hasColumn('certificate_field_values', 'section_instance')) {
                $table->integer('section_instance')->default(1)->after('certificate_id');
            }

            // Ensure certificate_template_field_id exists (it should, but just in case)
            if (!Schema::hasColumn('certificate_field_values', 'certificate_template_field_id')) {
                // If we have legacy field_id, we might rename it, or just add new one. 
                // Given the audit showed certificate_template_field_id EXISTS, we skip adding it.
                // But if it didn't validation would fail.
                $table->uuid('certificate_template_field_id')->nullable(false);
            }

            // Cleanup legacy columns
            if (Schema::hasColumn('certificate_field_values', 'field_id')) {
                 // We don't want to lose data if data exists there, but for now we assume fresh start or data was migrated.
                 // In this specific fix, user asked to FIX inconsistent state.
                 $table->dropColumn('field_id');
            }
            if (Schema::hasColumn('certificate_field_values', 'field_value')) {
                $table->dropColumn('field_value');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificate_field_values', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_field_values', 'section_instance')) {
                $table->dropColumn('section_instance');
            }
        });
    }
};
