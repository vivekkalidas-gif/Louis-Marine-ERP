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
        Schema::table('stock_movements', function (Blueprint $table) {
            // Use raw SQL because doctrine/dbal is missing
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE stock_movements MODIFY reference_id VARCHAR(36) NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
             \Illuminate\Support\Facades\DB::statement("ALTER TABLE stock_movements MODIFY reference_id BIGINT UNSIGNED NULL");
        });
    }
};
