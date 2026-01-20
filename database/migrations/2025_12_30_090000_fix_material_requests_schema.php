<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('material_requests');

        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('service_job_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignUuid('requested_by')->constrained('users');
            
            // Correct Foreign Key to Inventory
            $table->foreignId('inventory_item_id')->nullable()->constrained('inventory_items')->nullOnDelete();

            $table->string('item_name')->nullable();
            $table->integer('quantity');
            $table->string('urgency')->default('normal');
            $table->string('status')->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requests');
    }
};
