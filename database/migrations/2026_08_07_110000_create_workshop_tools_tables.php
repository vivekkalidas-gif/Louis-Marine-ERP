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
        Schema::create('workshop_tools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->nullable()->unique();
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('unit')->default('pcs');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('purchase_date')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tool_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('workshop_tool_id');
            $table->string('status')->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->uuid('issued_by')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->uuid('received_by')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workshop_tool_id')->references('id')->on('workshop_tools')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_requests');
        Schema::dropIfExists('workshop_tools');
    }
};
