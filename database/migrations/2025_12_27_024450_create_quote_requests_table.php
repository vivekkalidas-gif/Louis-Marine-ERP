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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_no')->unique(); // QR-2025-001
            $table->string('company_name')->nullable();
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'in_review', 'quoted', 'closed'])->default('new');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
