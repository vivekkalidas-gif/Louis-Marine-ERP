<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix "Class 'MaterialRequest' not found" by updating to full namespace
        DB::table('stock_movements')
            ->where('reference_type', 'MaterialRequest')
            ->update(['reference_type' => 'App\\Models\\MaterialRequest']);
    }

    public function down(): void
    {
        // Optional revert
        DB::table('stock_movements')
            ->where('reference_type', 'App\\Models\\MaterialRequest')
            ->update(['reference_type' => 'MaterialRequest']);
    }
};
