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
        // Disable foreign key checks to allow deletion of referenced items
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        // Delete all existing "demo" equipment items
        \Illuminate\Support\Facades\DB::table('equipment_items')->delete();
        
        // Re-enable foreign key checks
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
