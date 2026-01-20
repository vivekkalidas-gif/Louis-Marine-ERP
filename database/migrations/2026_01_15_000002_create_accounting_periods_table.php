<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "January 2026"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            
            // Prevent overlapping periods? 
            // For now, simple unique name or start_date check
            $table->unique(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_periods');
    }
};
