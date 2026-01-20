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
        // Safety check to avoid "table already exists" if partial failure occurred
        if (!Schema::hasTable('tool_requests')) {
            Schema::create('tool_requests', function (Blueprint $table) {
                $table->id();
                
                // Requester
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                
                // Tool - Using constrained() shorthand which assumes equipment_items table
                $table->foreignId('equipment_item_id')->constrained('equipment_items')->cascadeOnDelete();
                
                // Status Workflow
                $table->string('status')->default('pending'); // pending, issued, returned, rejected
                
                // Timestamps
                $table->timestamp('requested_at')->useCurrent();
                
                // Storekeeper actions (nullable FKs)
                $table->unsignedBigInteger('issued_by')->nullable();
                $table->timestamp('issued_at')->nullable();
                
                $table->unsignedBigInteger('received_by')->nullable();
                $table->timestamp('returned_at')->nullable();
                
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                
                $table->timestamps();
                
                // Manual FKs for nullable users to avoid complexity with constrained() on nullables if not careful
                $table->foreign('issued_by')->references('id')->on('users');
                $table->foreign('received_by')->references('id')->on('users');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_requests');
    }
};
