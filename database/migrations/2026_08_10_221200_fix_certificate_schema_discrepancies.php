<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix certificate_field_values table schema
        if (Schema::hasTable('certificate_field_values')) {
            Schema::table('certificate_field_values', function (Blueprint $table) {
                if (!Schema::hasColumn('certificate_field_values', 'certificate_template_field_id')) {
                    $table->uuid('certificate_template_field_id')->nullable()->after('certificate_id');
                }
                if (!Schema::hasColumn('certificate_field_values', 'value')) {
                    $table->text('value')->nullable()->after('section_instance');
                }
            });

            // Copy data if old column names field_id / field_value exist
            if (Schema::hasColumn('certificate_field_values', 'field_id') && Schema::hasColumn('certificate_field_values', 'certificate_template_field_id')) {
                DB::table('certificate_field_values')
                    ->whereNull('certificate_template_field_id')
                    ->whereNotNull('field_id')
                    ->update(['certificate_template_field_id' => DB::raw('field_id')]);
            }

            if (Schema::hasColumn('certificate_field_values', 'field_value') && Schema::hasColumn('certificate_field_values', 'value')) {
                DB::table('certificate_field_values')
                    ->whereNull('value')
                    ->whereNotNull('field_value')
                    ->update(['value' => DB::raw('field_value')]);
            }
        }

        // 2. Fix certificates table schema
        if (Schema::hasTable('certificates')) {
            Schema::table('certificates', function (Blueprint $table) {
                if (!Schema::hasColumn('certificates', 'assigned_verifier_id')) {
                    $table->uuid('assigned_verifier_id')->nullable();
                }
                if (!Schema::hasColumn('certificates', 'verified_at')) {
                    $table->timestamp('verified_at')->nullable();
                }
                if (!Schema::hasColumn('certificates', 'verification_notes')) {
                    $table->text('verification_notes')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
