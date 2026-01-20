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
            // Make service_job_id required (NOT NULL)
            // $table->uuid('service_job_id')->nullable(false)->change();
            
            // Add job_number for quick reference (denormalized)
            $table->string('job_number')->after('service_job_id')->nullable();
            
            // Add equipment_type for duplicate prevention
            $table->string('equipment_type')->after('job_number')->nullable();
            
            // Add unique constraint: one certificate per job+equipment+type
            // This prevents duplicate certificates for the same equipment on same job
            $table->unique(['service_job_id', 'equipment_type', 'certificate_type_id'], 'unique_cert_per_job_equipment');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropUnique('unique_cert_per_job_equipment');
            $table->dropColumn(['job_number', 'equipment_type']);
            $table->foreignUuid('service_job_id')->nullable()->change();
        });
    }
};
