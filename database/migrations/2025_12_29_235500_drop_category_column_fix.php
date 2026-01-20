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
        Schema::table('inventory_items', function (Blueprint $table) {
            // Drop index if exists (array syntax for index name generation)
            if (Schema::hasColumn('inventory_items', 'category')) {
                try {
                     $table->dropIndex(['category']); 
                } catch (\Exception $e) {}
                $table->dropColumn('category');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('category')->nullable(); // Make nullable in reverse to be safe
            $table->index('category');
        });
    }
};
