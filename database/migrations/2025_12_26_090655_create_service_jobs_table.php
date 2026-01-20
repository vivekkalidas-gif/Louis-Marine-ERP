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
        Schema::create('service_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('job_number')->unique(); // JOB-2023-0001
            $table->foreignUuid('vessel_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete(); // Engineer ID
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
