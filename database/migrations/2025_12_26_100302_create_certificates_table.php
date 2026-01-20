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
        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('certificate_type_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('vessel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('service_job_id')->nullable()->constrained()->nullOnDelete();
            
            $table->string('certificate_number')->unique()->nullable(); // Generated on Issue
            $table->string('status')->default('DRAFT'); // DRAFT, ISSUED, EXPIRED, REVOKED
            
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            $table->json('data')->nullable(); // Dynamic content
            
            $table->foreignUuid('created_by')->constrained('users'); // Engineer
            $table->foreignUuid('approved_by')->nullable()->constrained('users'); // PRO
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
