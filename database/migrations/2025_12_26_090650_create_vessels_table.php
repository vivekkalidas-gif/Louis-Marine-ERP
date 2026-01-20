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
        Schema::create('vessels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('imo_number')->unique()->nullable();
            $table->string('type')->nullable(); // Tanker, Cargo, Yacht, etc.
            $table->string('flag')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('status')->default('active'); // active, inactive, maintenance
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessels');
    }
};
