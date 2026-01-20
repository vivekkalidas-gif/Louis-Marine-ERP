<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Fix "Opening Stock" -> NULL
        DB::table('stock_movements')
            ->where('reference_type', 'Opening Stock')
            ->update(['reference_type' => null]);

        // 2. Fix "purchase_order" -> App\Models\PurchaseOrder
        DB::table('stock_movements')
            ->where('reference_type', 'purchase_order')
            ->update(['reference_type' => 'App\\Models\\PurchaseOrder']);
            
        // 3. Fix "Manual Outward" -> NULL (if missed)
        DB::table('stock_movements')
            ->where('reference_type', 'Manual Outward')
            ->update(['reference_type' => null]);
    }

    public function down(): void
    {
        // Revert logically if possible
        DB::table('stock_movements')
            ->where('reference_type', 'App\\Models\\PurchaseOrder')
            ->update(['reference_type' => 'purchase_order']);
    }
};
