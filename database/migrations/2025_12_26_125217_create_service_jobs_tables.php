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
        // Disable FK checks to allow dropping tables referenced by others (like invoices)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop old table if exists to start fresh (Development mode decision)
        Schema::dropIfExists('service_reports');
        Schema::dropIfExists('service_job_documents');
        Schema::dropIfExists('service_jobs');

        // Service Jobs Table
        Schema::create('service_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('job_number')->unique(); // e.g., JOB-2025-001
            $table->foreignUuid('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('vessel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('Open'); // Open, Assigned, In Progress, On Hold, Completed, Cancelled
            $table->string('priority')->default('Normal'); // Normal, High, Urgent
            $table->text('scope_of_work')->nullable();
            $table->string('reference_no')->nullable(); // PO Number
            $table->foreignUuid('assigned_engineer_id')->nullable()->constrained('users');
            $table->dateTime('scheduled_start')->nullable();
            $table->dateTime('scheduled_end')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Service Reports (Field Reports)
        Schema::create('service_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->foreignUuid('engineer_id')->constrained('users'); // Who created the report
            $table->date('report_date');
            $table->text('work_description');
            $table->text('recommendations')->nullable();
            $table->decimal('hours_worked', 5, 2)->default(0);
            $table->string('status')->default('Draft'); // Draft, Finalized
            $table->string('customer_signature_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Service Job Documents (for attachments like photos, POs)
        Schema::create('service_job_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('type')->default('Attachment'); // PO, Photo, Other
            $table->foreignUuid('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_jobs_tables');
    }
};
