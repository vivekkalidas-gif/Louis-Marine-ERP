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
        if (!Schema::hasTable('service_job_documents')) {
            Schema::create('service_job_documents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('service_job_id')->constrained('service_jobs')->cascadeOnDelete();
                $table->foreignUuid('uploaded_by')->constrained('users');
                $table->string('title');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->bigInteger('file_size')->nullable();
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
        Schema::dropIfExists('service_job_documents');
    }
};
