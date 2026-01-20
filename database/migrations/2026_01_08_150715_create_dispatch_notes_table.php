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
        Schema::create('dispatch_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dispatch_number')->unique();
            $table->date('date');
            $table->string('type'); // Calibration, Site Transfer, Return, etc.
            $table->uuid('service_job_id')->nullable();
            $table->string('receiver_name');
            $table->text('receiver_address')->nullable();
            $table->string('status')->default('Draft'); // Draft, Dispatched, Returned, Closed
            $table->foreignUuid('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('dispatch_note_id')->constrained('dispatch_notes')->onDelete('cascade');
            $table->string('description');
            $table->string('serial_number')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
        Schema::dropIfExists('dispatch_notes');
    }
};
