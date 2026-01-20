<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix "Class not found" errors by setting invalid polymorphic types to NULL.
        // We will rely on 'movement_type' and null reference to identify these.
        DB::table('stock_movements')
            ->whereIn('reference_type', ['Manual Inward', 'Stock Adjustment', 'Manual Outward'])
            ->update([
                'reference_type' => null,
                'reference_id' => null
            ]);
    }

    public function down(): void
    {
        // Cannot revert data loss, but this is a fix for invalid data.
    }
};
