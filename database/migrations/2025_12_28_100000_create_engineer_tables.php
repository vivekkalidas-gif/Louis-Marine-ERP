<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Job Assignments (Technician Tracking)
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('service_job_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->comment('Technician User ID')->constrained()->onDelete('cascade'); // Technician
            $table->foreignUuid('assigned_by')->constrained('users')->onDelete('cascade'); // Engineer/Manager
            $table->timestamp('assigned_at')->useCurrent();
            $table->decimal('hours_worked', 8, 2)->default(0);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->string('status')->default('assigned'); // assigned, in_progress, completed
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Material Requests
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('service_job_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('requested_by')->constrained('users'); // Engineer
            $table->foreignUuid('product_id')->nullable()->constrained('products'); // Inventory Item
            $table->string('item_name')->nullable(); // For non-inventory items
            $table->integer('quantity');
            $table->string('urgency')->default('normal'); // low, normal, high, critical
            $table->string('status')->default('pending'); // pending, approved, rejected, issued
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });

        // 3. Delivery Notes
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique(); // MDN-2025-XXXX
            $table->foreignUuid('service_job_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('created_by')->constrained('users');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_contact')->nullable();
            $table->string('signature_path')->nullable();
            $table->json('items')->nullable(); // Snapshot of items delivered
            $table->date('delivery_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
        Schema::dropIfExists('material_requests');
        Schema::dropIfExists('job_assignments');
    }
};
