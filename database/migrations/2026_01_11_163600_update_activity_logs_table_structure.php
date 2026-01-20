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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Rename columns to match Model expected names if they exist
            if (Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->renameColumn('subject_type', 'model_type');
            }
            if (Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->renameColumn('subject_id', 'model_id');
            }

            // Add 'module' column if it doesn't exist
            if (!Schema::hasColumn('activity_logs', 'module')) {
                $table->string('module')->nullable()->after('action');
            }

            // Add 'old_values' and 'new_values'
            if (!Schema::hasColumn('activity_logs', 'old_values')) {
                $table->json('old_values')->nullable()->after('module');
            }
            if (!Schema::hasColumn('activity_logs', 'new_values')) {
                $table->json('new_values')->nullable()->after('old_values');
            }

            // Add 'user_agent'
            if (!Schema::hasColumn('activity_logs', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            // Rename back
            if (Schema::hasColumn('activity_logs', 'model_type')) {
                $table->renameColumn('model_type', 'subject_type');
            }
            if (Schema::hasColumn('activity_logs', 'model_id')) {
                $table->renameColumn('model_id', 'subject_id');
            }

            // Drop columns
            $table->dropColumn(['module', 'old_values', 'new_values', 'user_agent']);
        });
    }
};
