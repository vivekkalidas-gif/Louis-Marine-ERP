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
        Schema::table('service_jobs', function (Blueprint $table) {
            if (!Schema::hasColumn('service_jobs', 'job_type')) {
                $table->string('job_type')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'priority')) {
                $table->string('priority')->default('Normal');
            }
            if (!Schema::hasColumn('service_jobs', 'job_source')) {
                $table->string('job_source')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'scheduled_at')) {
                $table->dateTime('scheduled_at')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'service_category')) {
                $table->string('service_category')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'service_types')) {
                $table->text('service_types')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'special_instructions')) {
                $table->text('special_instructions')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'location_details')) {
                $table->string('location_details')->nullable();
            }
            if (!Schema::hasColumn('service_jobs', 'scope_of_work')) {
                $table->text('scope_of_work')->nullable();
            }
        });
        // Renaming skipped to avoid failures
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_jobs', function (Blueprint $table) {
            $table->dropColumn([
                'job_type', 
                'priority', 
                'job_source', 
                'scheduled_at', 
                'service_category', 
                'service_types', 
                'special_instructions',
                'location_details',
                'scope_of_work'
            ]);
        });
    }


};
