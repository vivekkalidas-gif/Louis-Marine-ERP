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
        Schema::table('certificate_field_values', function (Blueprint $table) {
            // 1. Ensure `section_instance` exists
            if (!Schema::hasColumn('certificate_field_values', 'section_instance')) {
                $table->integer('section_instance')->default(1)->after('certificate_template_field_id');
            }

            // 2. Ensure `certificate_template_field_id` exists (it seems to exist already)
            // But just in case
            if (!Schema::hasColumn('certificate_field_values', 'certificate_template_field_id')) {
                 $table->uuid('certificate_template_field_id')->nullable();
                 // If we had to add it, we might need to populate it, but assuming new install or empty table for now if missing.
            }

            // 3. Ensure `value` exists (it seems to exist)
            if (!Schema::hasColumn('certificate_field_values', 'value')) {
                $table->longText('value')->nullable();
            }

            // 4. Drop legacy columns if they exist
            if (Schema::hasColumn('certificate_field_values', 'field_id')) {
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
        // No reverse for this fix, it's a one-way correction.
    }
};
